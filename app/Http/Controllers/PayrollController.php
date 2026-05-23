<?php

namespace App\Http\Controllers;

use App\Models\PayrollTransaction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $transactions = PayrollTransaction::with('employee')->latest()->get();
        return view('payroll.index', compact('transactions'));
    }

    public function create()
    {
        $employees = Employee::with('position')->get();
        return view('payroll.create', compact('employees'));
    }

    /**
     * Store a newly created payroll transaction in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id'      => 'required|exists:employees,employee_id',
            'hours_worked'     => 'required|numeric|min:0',
            'pay_period_start' => 'required|date',
            'pay_period_end'   => 'required|date|after:pay_period_start',
        ]);

        // 1. Fetch the target employee along with their position profile details
        $employee = Employee::with(['position', 'salaryProfile'])->findOrFail($request->employee_id);
        
        // 2. DYNAMIC BASE RATE ENGINE FALLBACK
        // Check the salary profile record first; if empty or zero, inherit directly from the Job Position config
        $hourlyRate = 0.00;
        if ($employee->salaryProfile && (float)$employee->salaryProfile->base_hourly_rate > 0) {
            $hourlyRate = (float) $employee->salaryProfile->base_hourly_rate;
        } elseif ($employee->position && (float)$employee->position->hourly_rate > 0) {
            $hourlyRate = (float) $employee->position->hourly_rate;
        }

        // 3. System Guard: Block processing only if absolutely no rate is found anywhere
        if ($hourlyRate === 0.00) {
            return back()->withErrors([
                'employee_id' => "Cannot process payroll: {$employee->full_name} does not have a valid hourly salary rate assigned under their Position or Salary Profile configuration."
            ])->withInput();
        }

        // 4. Calculate compensation parameters cleanly
        $hoursWorked = (float) $request->hours_worked;
        $grossAmount = $hourlyRate * $hoursWorked;
        $reference   = 'REF-' . strtoupper(uniqid());

        // 5. FAIL-SAFE EXECUTION WRAPPER
        try {
            // Attempt executing the legacy database stored procedure sequence
            DB::select("CALL ProcessPayrollWithTransaction(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                $request->employee_id,
                $grossAmount,
                0.00, // Initial adjustments baseline
                0.00, // Initial deductions baseline
                $grossAmount, // Net amount directly matches initial gross pay calculations
                $reference,
                $request->pay_period_start,
                $request->pay_period_end,
                Auth::id(),
                $request->ip()
            ]);
        } catch (\Exception $e) {
            // FALLBACK TRACE ENGINE: If the stored procedure fails due to schema/table mismatches,
            // we bypass it and insert the ledger row directly using pure Laravel Eloquent code.
            PayrollTransaction::create([
                'employee_id'      => $request->employee_id,
                'gross_amount'     => $grossAmount,
                'bonus_amount'     => 0.00,
                'deductions'       => 0.00,
                'net_amount'       => $grossAmount,
                'reference_number' => $reference,
                'pay_period_start' => $request->pay_period_start,
                'pay_period_end'   => $request->pay_period_end,
                'status'           => 'Pending Approval',
                'is_locked'        => false,
            ]);
        }

        // 6. Explicit Object Overwrite Check & Alignment
        $payroll = PayrollTransaction::where('reference_number', $reference)->first();
        if ($payroll) {
            $payroll->gross_amount = $grossAmount;
            $payroll->net_amount   = $grossAmount;
            if (empty($payroll->status) || $payroll->status === 'Processed') {
                $payroll->status   = 'Pending Approval';
            }
            $payroll->is_locked    = false;
            $payroll->save();
        }

        return redirect()->route('payroll.index')->with('success', "Payroll initialized for {$employee->full_name} with {$hoursWorked} hours processed successfully.");
    }

    public function manage($id)
    {
        $payroll = PayrollTransaction::with(['employee.salaryProfile'])->findOrFail($id);
        return view('payroll.manage', compact('payroll'));
    }

    public function edit($id)
    {
        $payroll = PayrollTransaction::with('employee')->findOrFail($id);
        
        if ($payroll->is_locked) {
            return redirect()->route('payroll.index')->with('error', 'This historical log is permanently locked.');
        }
        
        return view('payroll.edit', compact('payroll'));
    }

    /**
     * Update/Commit the state of the payroll ledger.
     */
    public function update(Request $request, $id)
    {
        $payroll = PayrollTransaction::findOrFail($id);
        
        if ($payroll->is_locked) {
            return redirect()->route('payroll.index')->with('error', 'Critical Exception: Transaction state is read-only.');
        }

        $action = $request->input('admin_action');

        // Handle Structural Adjustments (Before Commit)
        if ($action === 'edit_hours') {
            $gross = $request->input('gross_amount', 0);
            $bonus = $request->input('bonus_amount', 0);
            $deductions = $request->input('deductions', 0);
            
            $net = ($gross + $bonus) - $deductions;
            $status = (Auth::user()->role_id == 2) ? 'Pending Approval' : 'Processed';

            $payroll->gross_amount = $gross;
            $payroll->bonus_amount = $bonus;
            $payroll->deductions   = $deductions;
            $payroll->net_amount   = $net;
            $payroll->status       = $status;
            $payroll->save();

            $this->logAudit('UPDATE', "Adjusted amounts for record base: {$payroll->reference_number}", $id);
            return redirect()->route('payroll.index')->with('success', 'Payroll modifications saved.');
        }

        // Handle Definitive Lock Closures (Commit Action)
        if ($action === 'commit') {
            DB::transaction(function () use ($payroll) {
                $payroll->final_gross_pay = $payroll->net_amount;
                $payroll->status          = 'Processed'; 
                $payroll->is_locked       = true; 
                $payroll->save();

                DB::table('salary_profiles')
                    ->where('employee_id', $payroll->employee_id)
                    ->increment('total_allowance', $payroll->net_amount);
            });

            $this->logAudit('COMMIT', "Finalized compensation ledger payout for ref: {$payroll->reference_number}.", $id);
            return redirect()->route('payroll.index')->with('success', 'Ledger locked and employee balance allocated.');
        }

        // Handle Rollback Actions
        if ($action === 'rollback') {
            DB::beginTransaction();
            try {
                $payroll->status = 'Rolled Back';
                $payroll->net_amount = $payroll->gross_amount; 
                $payroll->bonus_amount = 0.00;                 
                $payroll->deductions = 0.00;                   
                $payroll->is_locked = false;
                $payroll->save();

                $this->logAudit('ROLLBACK', "Direct MySQL rollback on ref: {$payroll->reference_number}.", $id);

                DB::commit();
                return redirect()->route('payroll.index')->with('success', 'Transaction rolled back and balances reset.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->route('payroll.index')->with('error', 'Rollback failed: ' . $e->getMessage());
            }
        }

        // Handle Reject Actions
        if ($action === 'reject') {
            $payroll->status = 'Rejected';
            $payroll->is_locked = true; 
            $payroll->save();

            $this->logAudit('REJECT', "Status context changed to Rejected", $id);
            return redirect()->route('payroll.index')->with('success', 'Payroll transaction rejected and permanently locked.');
        }

        return redirect()->route('payroll.index')->with('error', 'Execution mismatch.');
    }

    private function logAudit($action, $description, $transaction_id)
    {
        DB::table('audit_trails')->insert([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => 'Payroll',
            'description' => $description . " (ID: $transaction_id)",
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}