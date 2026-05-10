<?php

namespace App\Http\Controllers;

use App\Models\PayrollTransaction;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PayrollController extends Controller {
    public function store(Request $request) {
        $request->validate([
            'employee_id' => 'required|exists:employees,employee_id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Fetch Employee and their Position's Basic Pay
            $employee = Employee::with('position')->findOrFail($request->employee_id);
            
            $gross = $employee->position->basic_pay; 
            $bonus = $request->bonus ?? 0;
            $deductions = $request->deductions ?? 0;
            $net = ($gross + $bonus) - $deductions;

            // 2. Create the Transaction Record
            $payroll = PayrollTransaction::create([
                'employee_id' => $employee->employee_id,
                'gross_amount' => $gross,
                'bonus_amount' => $bonus,
                'deductions' => $deductions,
                'net_amount' => $net,
                'pay_period_start' => $request->start_date,
                'pay_period_end' => $request->end_date,
                'reference_number' => 'PAY-' . strtoupper(Str::random(10)),
                'status' => 'Completed'
            ]);

            // 3. Create the Audit Trail entry
            DB::table('audit_trails')->insert([
                'user_id' => auth()->id(),
                'module' => 'Payroll',
                'action' => 'CREATE_TRANSACTION',
                'new_values' => "Payroll processed for {$employee->full_name} - Ref: {$payroll->reference_number}",
                'ip_address' => $request->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Payroll Transaction #' . $payroll->reference_number . ' generated.');
        });
    }
}