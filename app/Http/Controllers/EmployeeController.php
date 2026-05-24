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
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display the list of employees.
     */
    public function index() 
    {
        $user = Auth::user();
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
        // Enforce state engine compliance rules: structural components must be active
        $positions = Position::where('is_active', true)->get(); 
        $branches = Branch::where('is_active', true)->get();
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
            // Backend safeguard protecting against injected/spoofed payloads of inactive metrics
            'position_id' => ['required', Rule::exists('positions', 'position_id')->where('is_active', true)],
            'branch_id'   => ['required', Rule::exists('branches', 'branch_id')->where('is_active', true)],
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
        
        // Show all active records, but merge the employee's current selection so historical assignments don't break
        $positions = Position::where('is_active', true)->orWhere('position_id', $employee->position_id)->get();
        $branches = Branch::where('is_active', true)->orWhere('branch_id', $employee->branch_id)->get();
        
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
            'password'    => 'nullable|string|min:6',
            'position_id' => ['required', Rule::exists('positions', 'position_id')->where(function($q) use ($employee) {
                $q->where('is_active', true)->orWhere('position_id', $employee->position_id);
            })],
            'branch_id'   => ['required', Rule::exists('branches', 'branch_id')->where(function($q) use ($employee) {
                $q->where('is_active', true)->orWhere('branch_id', $employee->branch_id);
            })],
        ]);

        $position = Position::findOrFail($request->position_id);

        return DB::transaction(function () use ($request, $employee, $position) {
            $userData = [
                'name'    => $request->name,
                'email'   => $request->email,
                'role_id' => $position->role_id,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $employee->user->update($userData);

            $employee->update([
                'position_id' => $request->position_id,
                'branch_id'   => $request->branch_id,
                'full_name'   => $request->name,
            ]);

            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'UPDATE_PERSONNEL',
                'module'      => 'Staff Directory',
                'description' => "Updated profile for: {$request->name}. Position: {$position->position_title}",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('employees.index')->with('success', 'Staff record updated successfully.');
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
            \App\Models\AuditTrail::where('user_id', $user->user_id)->delete();

            $employee->delete();
            $user->delete(); 

            return redirect()->route('employees.index')->with('success', 'Staff record removed.');
        });
    }
    public function verifyEmail($id)
{
    $employee = Employee::findOrFail($id);
    
    if ($employee->user) {
        $employee->user->update([
            'email_verified_at' => now()
        ]);
    }

    return redirect()->back()->with('success', "Personnel network profile entry [{$employee->full_name}] authorized successfully.");
}
}