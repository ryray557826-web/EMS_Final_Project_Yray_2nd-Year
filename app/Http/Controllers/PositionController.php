<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\AuditTrail; // CRITICAL: Added this import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PositionController extends Controller
{
    /**
     * Display a listing of job positions.
     */
    public function index()
    {
        // Security: Only Super Admins (Role 1) should manage pay grades
        if (Auth::user()->role_id !== 1) {
            abort(403, 'Unauthorized access to job structure configuration.');
        }

        $positions = Position::all();
        return view('positions.index', compact('positions'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        return view('positions.create');
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'position_title' => 'required|string|max:255',
            'job_level'      => 'required|string',
            'basic_pay'      => 'required|numeric|min:0',
        ]);

        $position = Position::create($validated);

        // Security Audit
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'CREATE',
            'module' => 'Positions',
            'description' => "New job tier established: {$position->position_title} ({$position->job_level}) at ₱" . number_format($position->basic_pay, 2) . "/day"
        ]);

        return redirect()->route('positions.index')->with('status', 'Position successfully added to the system.');
    }

    /**
     * Remove the specified position.
     */

    public function edit($id)
{
    $position = Position::findOrFail($id);
    // Reuse your create view or a separate edit view
    return view('positions.create', compact('position')); 
}

// Also add the update method for when they click save:
public function update(Request $request, $id)
{
    $position = Position::findOrFail($id);
    $position->update($request->all());
    
    return redirect()->route('positions.index')->with('status', 'Position updated.');
}
    public function destroy($id)
    {
        $position = Position::findOrFail($id);

        // Safety check: Don't orphan employees
        if ($position->employees()->count() > 0) {
            return redirect()->back()->with('error', 'STRICT DENIAL: Staff are currently assigned to this position. Reassign them before deletion.');
        }

        // Log the deletion to Audit Trail
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'DELETE',
            'module' => 'Positions',
            'description' => "Terminated job position: {$position->position_title}"
        ]);

        $position->delete();
        return redirect()->route('positions.index')->with('status', 'Position removed from the organizational structure.');
    }
}