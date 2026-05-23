<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalaryProfileController extends Controller
{
    public function index()
    {
        // Enforce strict Super Admin restriction
        if (Auth::user()->role_id != 1) abort(403);

        // 1. Fetch employees with their basic relations bound
        $employees = Employee::with(['position', 'branch', 'salaryProfile'])->get();

        // 2. AUTOMATED DATA SYNC ENGINE:
        // Loops through your employees and ensures their structural salary profile 
        // inherits the hourly rate defined inside their assigned Job Position.
        foreach ($employees as $emp) {
            $positionDefaultRate = $emp->position->hourly_rate ?? 0.00;

            DB::table('salary_profiles')->updateOrInsert(
                ['employee_id' => $emp->employee_id],
                [
                    // If a custom profile rate already exists, preserve it. Otherwise, inherit the position rate.
                    'base_hourly_rate' => DB::table('salary_profiles')
                        ->where('employee_id', $emp->employee_id)
                        ->where('base_hourly_rate', '>', 0)
                        ->exists() 
                            ? DB::raw('base_hourly_rate') 
                            : $positionDefaultRate,
                    'updated_at' => now()
                ]
            );
        }

        // 3. Re-fetch clean dataset with synchronized balances for display
        $employees = Employee::with(['position', 'branch', 'salaryProfile'])->get();
        
        return view('salary-profiles.index', compact('employees'));
    }
}