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

        // 2. AUTOMATED DATA SYNC ENGINE
        foreach ($employees as $emp) {
            $positionDefaultRate = $emp->position->hourly_rate ?? 0.00;
            
            // Extract the position key safely from the employee relationship tier
            $positionId = $emp->position_id ?? ($emp->position->position_id ?? null);

            // If the employee somehow has no position assigned, skip to prevent database crashes
            if (!$positionId) {
                continue;
            }

            DB::table('salary_profiles')->updateOrInsert(
                ['employee_id' => $emp->employee_id],
                [
                    // Pass the required position tracking identity key
                    'position_id'      => $positionId,
                    
                    // Keep existing rates or inherit from the parent job structure configuration
                    'base_hourly_rate' => DB::table('salary_profiles')
                        ->where('employee_id', $emp->employee_id)
                        ->where('base_hourly_rate', '>', 0)
                        ->exists() 
                            ? DB::raw('base_hourly_rate') 
                            : $positionDefaultRate,
                    'updated_at'       => now()
                ]
            );
        }

        // 3. Re-fetch clean dataset with synchronized balances for display
        $employees = Employee::with(['position', 'branch', 'salaryProfile'])->get();
        
        return view('salary-profiles.index', compact('employees'));
    }
}