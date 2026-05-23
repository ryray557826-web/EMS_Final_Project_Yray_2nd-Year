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

        // Fetch current records collection
        $employees = Employee::with(['position', 'salaryProfile'])->get();
        
        // Auto-Initialization Engine: Detects missing table rows and automatically creates them
        foreach ($employees as $emp) {
            if (!$emp->salaryProfile) {
                DB::table('salary_profiles')->insertOrIgnore([
                    'employee_id'       => $emp->employee_id,
                    'base_hourly_rate'  => 0.00, // Unconfigured baseline state
                    'total_allowance'   => 0.00,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
            }
        }

        // Re-fetch collection with freshly generated baseline profiles included
        $employees = Employee::with(['position', 'salaryProfile'])->get();
        
        return view('salary-profiles.index', compact('employees'));
    }

    /**
     * Store or update the employee base hourly compensation rate.
     */
    public function storeOrUpdate(Request $request, $employee_id)
    {
        if (Auth::user()->role_id != 1) abort(403);

        $request->validate([
            'base_hourly_rate' => 'required|numeric|min:0',
        ]);

        DB::table('salary_profiles')
            ->updateOrInsert(
                ['employee_id' => $employee_id],
                [
                    'base_hourly_rate' => (float) $request->input('base_hourly_rate'),
                    'updated_at'       => now()
                ]
            );

        return back()->with('success', 'Base hourly compensation rate successfully updated.');
    }
}