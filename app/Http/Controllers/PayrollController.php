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
        $employees = Employee::all();
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

    // Pull employee along with their salary profile definition
    $employee = Employee::with('salaryProfile')->findOrFail($request->employee_id);
    
    // Fail-safe check: Ensure a rate actually exists so it doesn't silently resolve to ₱0.00
    if (!$employee->salaryProfile || !$employee->salaryProfile->base_hourly_rate) {
        return back()->withErrors([
            'employee_id' => "The profile for {$employee->full_name} does not have a configured Base Hourly Rate in the database."
        ])->withInput();
    }

    // Dynamic clean mathematical calculation formula applied universally to all profiles
    $hourlyRate  = (float) $employee->salaryProfile->base_hourly_rate;
    $hoursWorked = (float) $request->hours_worked;
    $grossAmount = $hourlyRate * $hoursWorked;

    $reference = 'REF-' . strtoupper(uniqid());

    // Execute core database processing engine allocation step
    DB::select("CALL ProcessPayrollWithTransaction(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        $request->employee_id,
        $grossAmount,
        0.00, // Initial bonus tracking reset
        0.00, // Initial deductions reset
        $grossAmount, // Net pay starts matched directly with calculated gross output
        $reference,
        $request->pay_period_start,
        $request->pay_period_end,
        Auth::id(),
        $request->ip()
    ]);

    // Force default state structures explicitly 
    $payroll = \App\Models\PayrollTransaction::where('reference_number', $reference)->first();
    if ($payroll) {
        $payroll->status = 'Pending Approval';
        $payroll->is_locked = false;
        $payroll->save();
    }

    return redirect()->route('payroll.index')->with('success', 'Payroll record processed and calculated successfully.');
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

        // 1. Handle Structural Adjustments (Before Commit)
        if ($action === 'edit_hours') {
            $gross = $request->input('gross_amount', 0);
            $bonus = $request->input('bonus_amount', 0);
            $deductions = $request->input('deductions', 0);
            
            // NET PAY SOLUTION: Explicitly force the mathematical calculation here
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

        // 2. Handle Definitive Lock Closures (Commit Action)
        if ($action === 'commit') {
            DB::transaction(function () use ($payroll) {
                // NET PAY SOLUTION: Map final_gross_pay column to the computed net take-home balance value
                $payroll->final_gross_pay = $payroll->net_amount;
                $payroll->status          = 'Processed'; 
                $payroll->is_locked       = true; // Write-protects the transaction ledger row
                $payroll->save();

                // NET PAY SOLUTION: Increment allowance profile with net_amount (including bonuses/deductions)
                DB::table('salary_profiles')
                    ->where('employee_id', $payroll->employee_id)
                    ->increment('total_allowance', $payroll->net_amount);
            });

            $this->logAudit('COMMIT', "Finalized compensation ledger payout for ref: {$payroll->reference_number}.", $id);
            return redirect()->route('payroll.index')->with('success', 'Ledger locked and employee balance allocated.');
        }

        // 3. Handle Rollback Actions
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

        // 4. Handle Reject Actions
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