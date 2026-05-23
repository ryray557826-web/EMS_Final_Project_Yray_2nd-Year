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
        $manager = Auth::user();

        // 1. Fetch staff members belonging to this manager's branch_id (excluding the manager)
        $employees = Employee::where('branch_id', $manager->branch_id)
            ->where('employee_id', '!=', $manager->employee_id) 
            ->get();

        // 2. Fetch historical scorecards specifically for this branch_id
        $scorecards = KpiScorecard::with('employee')
            ->whereHas('employee', function ($query) use ($manager) {
                $query->where('branch_id', $manager->branch_id);
            })
            ->latest()
            ->get();

        // Pass BOTH variables back to your single dashboard view
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