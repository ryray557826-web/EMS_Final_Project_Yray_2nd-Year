<?php

namespace App\Http\Controllers;

use App\Models\AttendanceLog;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceLogController extends Controller
{
    public function index()
{
    $user = Auth::user();

    // Safety: Create employee profile if it doesn't exist (for first-time admins)
    if (!$user->employee) {
        $this->autoCreateEmployee($user);
        $user->load('employee'); 
    }

    // Combined Logic: Get today's logs for this specific branch/user
    // Using Asia/Manila ensures the "Today" check matches your local time
    $logs = AttendanceLog::with('employee')
        ->where('employee_id', $user->employee->employee_id)
        ->whereDate('created_at', now()->timezone('Asia/Manila'))
        ->latest()
        ->get();

    return view('attendance.index', compact('logs'));
}

    public function store(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;
        $action = $request->input('action');

        // Check for an active session (Where time_out is null)
        $activeLog = AttendanceLog::where('employee_id', $employee->employee_id)
            ->whereNull('time_out')
            ->first();

        if ($action === 'clock_in') {
            if ($activeLog) {
                return redirect()->back()->with('error', 'STRICT DENIAL: You are already clocked in.');
            }

            AttendanceLog::create([
                'employee_id' => $employee->employee_id,
                'branch_id'   => $employee->branch_id,
                'time_in'     => now(),
                'status'      => 'Verified'
            ]);

            return redirect()->back()->with('success', 'Shift started. System is recording.');
        } 
        
        if ($action === 'clock_out') {
            if (!$activeLog) {
                return redirect()->back()->with('error', 'STRICT DENIAL: No active shift found.');
            }

            $activeLog->update(['time_out' => now()]);

            return redirect()->back()->with('success', 'Shift ended. Data synchronized.');
        }
        // Inside your store method
        $activeLog = AttendanceLog::where('employee_id', $employee->employee_id)
            ->whereNull('time_out')
            ->first();
        return redirect()->back();
    }

    private function autoCreateEmployee($user)
    {
        $defaultPosition = Position::first();
        Employee::create([
            'user_id'            => $user->user_id,
            'position_id'        => $defaultPosition->position_id ?? 1,
            'branch_id'          => 1,
            'full_name'          => $user->name,
            'employee_id_number' => 'SPL-' . strtoupper(substr(uniqid(), -5)),
            'status'             => 'Active'
        ]);
    }

    public function dashboard()
    {
        return view('dashboard');
    }
}