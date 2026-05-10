<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Position;      // Added this for line 70
use App\Models\SalaryProfile; // Added this for line 71
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;   // <--- This fixes the current error
class EmployeeController extends Controller
{
    /**
     * Display the list of employees based on role permissions.
     */
    public function index() 
    {
        $user = Auth::user();
        $query = Employee::with(['user', 'position', 'branch']);

        if ($user->role_id == 1) {
            $employees = $query->latest()->get(); // Super Admin sees all
        } else {
            // Admin sees only their branch
            $branchId = $user->employee->branch_id ?? 0;
            $employees = $query->where('branch_id', $branchId)->latest()->get();
        }

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $positions = Position::all();
        $branches = Branch::all();
        return view('employees.create', compact('positions', 'branches'));
    }

    /**
     * Store the new User and Employee records.
     */
    public function store(Request $request)
{
    return DB::transaction(function () use ($request) {
        // 1. Create User (Breeze)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 3,
        ]);

        // 2. Create Employee Profile
        $employee = Employee::create([
            'user_id' => $user->user_id,
            'branch_id' => $request->branch_id,
            'position_id' => $request->position_id,
            'full_name' => $request->name,
            'employee_id_number' => 'SPL-' . strtoupper(Str::random(5)),
            'status' => 'Active',
        ]);

        // 3. Create Salary Profile (using rate from Position table)
        $position = Position::find($request->position_id);
        SalaryProfile::create([
            'employee_id' => $employee->employee_id,
            'base_hourly_rate' => $position->base_hourly_rate,
        ]);

        return redirect()->route('employees.index')->with('success', 'Full profile deployed.');
    });
}

    /**
     * Show the edit form for an existing employee.
     */
    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        $positions = Position::all();
        $branches = Branch::all();
        return view('employees.edit', compact('employee', 'positions', 'branches'));
    }

    /**
     * Update the Employee and User records.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->user_id . ',user_id',
            'position_id' => 'required|exists:positions,position_id',
            'branch_id' => 'required|exists:branches,branch_id',
        ]);

        // Update User
        $employee->user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update Employee
        $employee->update([
            'position_id' => $request->position_id,
            'branch_id' => $request->branch_id,
        ]);

        // Audit Log for the update
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE',
            'module' => 'Employees',
            'description' => "Updated personnel details for: {$request->name}"
        ]);

        return redirect()->route('employees.index')->with('success', 'Staff record synchronized successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $name = $employee->user->name;

        // Note: You may want to delete the user or just set status to inactive
        $employee->user->delete(); 
        $employee->delete();

        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE',
            'module' => 'Employees',
            'description' => "Removed staff record for: $name"
        ]);

        return redirect()->route('employees.index')->with('success', 'Staff record removed from system.');
    }
}