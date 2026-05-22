<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\Position;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    /**
     * Display the list of employees.
     */
    public function index() 
    {
        $user = Auth::user();
        // FIXED: Eager loading included to allow access to $employee->position->position_title
        $query = Employee::with(['user', 'position', 'branch']);

        if ($user->role_id == 1) {
            $employees = $query->latest()->get(); 
        } else {
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
        $request->validate([
            'full_name'   => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:6',
            'position_id' => 'required|exists:positions,position_id',
            'branch_id'   => 'required|exists:branches,branch_id',
        ]);

        $position = Position::findOrFail($request->position_id);

        return DB::transaction(function () use ($request, $position) {
            $user = User::create([
                'name'     => $request->full_name, 
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role_id'  => $position->role_id, 
            ]);

            $generatedEmpNumber = 'EMP-' . strtoupper(substr($request->full_name, 0, 2)) . '-' . rand(1000, 9999);

            $employee = Employee::create([
                'user_id'            => $user->user_id, 
                'employee_id_number' => $generatedEmpNumber,
                'full_name'          => $request->full_name,
                'position_id'        => $request->position_id,
                'branch_id'          => $request->branch_id,
                'status'             => 'Active',
            ]);

            AuditTrail::create([
                'user_id'     => Auth::id() ?? $user->user_id, 
                'action'      => 'CREATE_PERSONNEL',
                'module'      => 'Staff Directory',
                'description' => "Account generated for: {$employee->full_name}. ID: {$employee->employee_id_number}. Role inherited: #{$user->role_id}.",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('employees.index')->with('success', 'Personnel record successfully generated.');
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
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $employee->user_id . ',user_id',
            'position_id' => 'required|exists:positions,position_id',
            'branch_id'   => 'required|exists:branches,branch_id',
        ]);

        $position = Position::findOrFail($request->position_id);

        return DB::transaction(function () use ($request, $employee, $position) {
            $employee->user->update([
                'name'    => $request->name,
                'email'   => $request->email,
                'role_id' => $position->role_id,
            ]);

            $employee->update([
                'position_id' => $request->position_id,
                'branch_id'   => $request->branch_id,
                'full_name'   => $request->name, // Keeping name synced
            ]);

            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'UPDATE',
                'module'      => 'Employees',
                'description' => "Updated details for: {$request->name}. Position sync: {$position->position_title}",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('employees.index')->with('success', 'Staff record updated and synchronized.');
        });
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy($id)
{
    $employee = Employee::findOrFail($id);
    $user = $employee->user; 
    $name = $user->name;

    return DB::transaction(function () use ($employee, $user, $name) {
        // 1. Manually delete the related audit trails first
        \App\Models\AuditTrail::where('user_id', $user->user_id)->delete();

        // 2. Now delete the records
        $employee->delete();
        $user->delete(); 

        // 3. Optional: You can't log the deletion to AuditTrail 
        // using the deleted user ID, so skip that specific line.

        return redirect()->route('employees.index')->with('success', 'Staff record removed.');
    });
}
}