<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\AuditTrail; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    /**
     * Display a listing of job positions.
     */
    public function index()
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized access to job structure configuration.');
        }

        // MODIFIED: Added with('employees') to optimize performance for the assignment check
        $positions = Position::with('employees')->get();
        return view('positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        return view('positions.create');
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'job_level'      => 'required|string',
            'hourly_rate'    => 'required|numeric|min:0',
            'role_id'        => 'required|integer|in:1,2,3',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $position = Position::create($validated);

            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'CREATE_POSITION',
                'module'      => 'Job Structure',
                'description' => "New job tier established: {$position->position_title} ({$position->job_level}) linked to Role ID: {$position->role_id} at ₱" . number_format($position->hourly_rate, 2) . "/hr",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('positions.index')->with('status', 'Position successfully added to the system.');
        });
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit($id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $position = Position::findOrFail($id);
        // MODIFIED: Ensure we explicitly pass the position object
        return view('positions.create', compact('position')); 
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $position = Position::findOrFail($id);

        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'job_level'      => 'required|string',
            'hourly_rate'    => 'required|numeric|min:0',
            'role_id'        => 'required|integer|in:1,2,3',
        ]);

        return DB::transaction(function () use ($position, $validated, $request) {
            $position->update($validated);

            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'UPDATE_POSITION',
                'module'      => 'Job Structure',
                'description' => "Modified structural parameters for tier: {$position->position_title}. Role Level: {$position->role_id}, Rate: ₱" . number_format($position->hourly_rate, 2) . "/hr",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('positions.index')->with('status', 'Position updated.');
        });
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy($id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $position = Position::findOrFail($id);

        // Verification using the relationship loaded in index or explicitly checked here
        if ($position->employees()->count() > 0) {
            return redirect()->back()->with('error', 'STRICT DENIAL: Staff are currently assigned to this position. Reassign them before deletion.');
        }

        return DB::transaction(function () use ($position) {
            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'DELETE_POSITION',
                'module'      => 'Job Structure',
                'description' => "Terminated job position structure: {$position->position_title} ({$position->job_level})",
                'ip_address'  => request()->ip(),
            ]);

            $position->delete();
            return redirect()->route('positions.index')->with('status', 'Position removed from the organizational structure.');
        });
    }
    /**
     * Toggle the active status of the specified position.
     */
    public function toggle(Request $request, $id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $position = Position::findOrFail($id);

        return DB::transaction(function () use ($position, $request) {
            // Flip the boolean status engine flag
            $position->is_active = !$position->is_active;
            $position->save();

            $statusText = $position->is_active ? 'Activated' : 'Deactivated';

            // Maintain compliance tracking sequence
            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'TOGGLE_POSITION_STATUS',
                'module'      => 'Job Structure',
                'description' => "Job tier status toggled for: {$position->position_title} ({$position->job_level}). Current state: {$statusText}",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('positions.index')->with('status', "Position tier successfully {$statusText}.");
        });
    }
}