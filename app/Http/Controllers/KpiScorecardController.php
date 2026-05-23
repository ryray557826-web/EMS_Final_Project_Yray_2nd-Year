<?php

namespace App\Http\Controllers;

use App\Models\KpiScorecard;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KpiScorecardController extends Controller
{
    /**
     * Display a comprehensive listing of performance metrics and dropdown personnel.
     */
    public function index()
{
    $user = Auth::user();

    // CASE 1: Logged-in user is a Super Admin (Role 1)
    if ($user->role_id == 1) {
        // Super Admins see ALL personnel and ALL historical records across the system
        $employees = Employee::all(); 
        $scorecards = KpiScorecard::with(['employee.branch'])->latest()->get();
    } 
    // CASE 2: Logged-in user is a Branch Manager or other staff
    else {
        // Safe fallback chain to discover the manager's branch_id
        $branchId = $user->branch_id;

        // If not directly on User, check if there's an associated employee relation
        if (!$branchId && isset($user->employee)) {
            $branchId = $user->employee->branch_id;
        }

        // If still not found, search the employees table by matching name/user metrics
        if (!$branchId) {
            $matchingEmployee = Employee::where('full_name', $user->name)
                ->orWhere('employee_id', $user->employee_id ?? null)
                ->first();
            $branchId = $matchingEmployee ? $matchingEmployee->branch_id : null;
        }

        // If the branch cannot be resolved, return empty collections gracefully
        if (!$branchId) {
            return view('kpi.index', [
                'employees' => collect(),
                'scorecards' => collect()
            ])->with('error', 'Unable to resolve your assigned branch branch.');
        }

        // Fetch staff members belonging to this resolved branch (excluding the manager)
        $employees = Employee::where('branch_id', $branchId)
            ->where('full_name', '!=', $user->name) 
            ->get();

        // Fetch historical scorecards belonging strictly to this branch
        $scorecards = KpiScorecard::with(['employee.branch'])
            ->whereHas('employee', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->latest()
            ->get();
    }

    // Pass the precisely filtered variables directly to the view
    return view('kpi.index', compact('scorecards', 'employees'));
}

    /**
     * Create a performance record and register a creation audit log.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'      => 'required|exists:employees,employee_id',
            'evaluation_score' => 'required|numeric|between:0,100',
            'remarks'          => 'nullable|string|max:500'
        ]);

        $kpi = KpiScorecard::create($validated);
        $employee = Employee::find($request->employee_id);

        // Security Trace: Register creation
        DB::table('audit_trails')->insert([
            'user_id'     => Auth::id(),
            'action'      => 'CREATE',
            'module'      => 'KPI Scorecards',
            'description' => "Evaluated KPI performance metrics for personnel: {$employee->full_name} at {$request->evaluation_score}%",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->route('kpi.index')->with('success', 'Performance evaluations injected successfully.');
    }

    /**
     * Show edit form for evaluation data modifiers.
     */
    public function edit($id)
    {
        $kpi = KpiScorecard::with('employee')->findOrFail($id);
        return view('kpi.edit', compact('kpi'));
    }

    /**
     * Update specified metric configurations and register an alteration log.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'evaluation_score' => 'required|numeric|between:0,100',
            'remarks'          => 'required|string|max:500'
        ]);

        $kpi = KpiScorecard::with('employee')->findOrFail($id);
        $oldScore = $kpi->evaluation_score;

        $kpi->update([
            'evaluation_score' => $request->evaluation_score,
            'remarks'          => $request->remarks
        ]);

        // Security Trace: Register structural updates
        DB::table('audit_trails')->insert([
            'user_id'     => Auth::id(),
            'action'      => 'UPDATE',
            'module'      => 'KPI Scorecards',
            'description' => "Altered score records for personnel: {$kpi->employee->full_name}. Shifted baseline from {$oldScore}% down/up to {$request->evaluation_score}%",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->route('kpi.index')->with('success', 'Staff metric adjustment synchronized.');
    }

    /**
     * Purge record data targets and trace removals.
     */
    public function destroy($id)
    {
        $kpi = KpiScorecard::with('employee')->findOrFail($id);
        $targetName = $kpi->employee->full_name;
        $scoreSnapshot = $kpi->evaluation_score;

        $kpi->delete();

        // Security Trace: Register data removal
        DB::table('audit_trails')->insert([
            'user_id'     => Auth::id(),
            'action'      => 'DELETE',
            'module'      => 'KPI Scorecards',
            'description' => "Purged performance metrics row mapping to personnel: {$targetName} (Terminated record score snapshot: {$scoreSnapshot}%)",
            'ip_address'  => request()->ip(),
            'created_at'  => now(),
            'updated_at'  => now()
        ]);

        return redirect()->route('kpi.index')->with('success', 'Score tracking record dropped from system data blocks.');
    }
}