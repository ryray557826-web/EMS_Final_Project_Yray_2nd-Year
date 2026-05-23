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

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'hours_worked' => 'required|numeric|min:0',
            'pay_period_start' => 'required|date',
            'pay_period_end' => 'required|date|after:pay_period_start',
        ]);

        $employee = Employee::with('salaryProfile')->findOrFail($request->employee_id);
        $hourlyRate = $employee->salaryProfile->base_hourly_rate ?? 0;
        $grossAmount = $hourlyRate * $request->hours_worked;

        $reference = 'REF-' . strtoupper(uniqid());

        // 1. Run core database stored procedure
        DB::select("CALL ProcessPayrollWithTransaction(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $request->employee_id,
            $grossAmount,
            0.00, // Initial bonus
            0.00, // Initial deductions
            $grossAmount, // Initial net
            $reference,
            $request->pay_period_start,
            $request->pay_period_end,
            Auth::id(),
            $request->ip()
        ]);

        $newPayroll = PayrollTransaction::where('reference_number', $reference)->first();
        
        if ($newPayroll) {
            // CRITICAL FIX: If Super Admin, completely skip staging, calculate final gross, and commit instantly
            if (Auth::user()->role_id == 1) {
                DB::transaction(function () use ($newPayroll) {
                    $totalGrossCalculated = $newPayroll->gross_amount + $newPayroll->bonus_amount;

                    $newPayroll->final_gross_pay = $totalGrossCalculated;
                    $newPayroll->status          = 'Processed';
                    $newPayroll->is_locked       = true; // Lock immediately
                    $newPayroll->save();

                    // Instantly add to employee balance ledger
                    DB::table('salary_profiles')
                        ->where('employee_id', $newPayroll->employee_id)
                        ->increment('total_allowance', $totalGrossCalculated);
                });

                $this->logAudit('COMMIT', "Super Admin auto-processed and locked ledger row: {$reference}", $newPayroll->transaction_id);
                return redirect()->route('payroll.index')->with('success', 'Payroll calculated, finalized, and balance allocated instantly.');
            } else {
                // Branch Admin: Keep open as an editable request change staging unit
                $newPayroll->status = 'Pending Approval';
                $newPayroll->is_locked = false;
                $newPayroll->save();
                
                return redirect()->route('payroll.index')->with('success', 'Branch modification request logged successfully.');
            }
        }

        return redirect()->route('payroll.index')->with('error', 'Error compiling transaction state data.');
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

    public function update(Request $request, $id)
    {
        $payroll = PayrollTransaction::findOrFail($id);
        
        if ($payroll->is_locked) {
            return redirect()->route('payroll.index')->with('error', 'Critical Exception: Transaction state is read-only.');
        }

        $action = $request->input('admin_action');

        // 1. Handle Structural Adjustments
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

        // 2. Handle Definitive Lock Closures (Commit Action)
        if ($action === 'commit') {
            DB::transaction(function () use ($payroll) {
                // FIX: Calculate total comprehensive gross pay (Base Gross + Bonuses added)
                $totalGrossCalculated = $payroll->gross_amount + $payroll->bonus_amount;

                $payroll->final_gross_pay = $totalGrossCalculated;
                $payroll->status          = 'Processed'; 
                $payroll->is_locked       = true;        
                $payroll->save();

                DB::table('salary_profiles')
                    ->where('employee_id', $payroll->employee_id)
                    ->increment('total_allowance', $totalGrossCalculated);
            });

            $this->logAudit('COMMIT', "Finalized compensation ledger payout.", $id);
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

        // 4. Handle Standard Status State Mutations (Reject Action)
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