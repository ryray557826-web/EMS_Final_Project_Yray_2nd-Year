<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * Display the listing of organizational branches.
     */
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form to create a new operational branch.
     */
    public function create()
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized access to infrastructure configuration.');
        }

        return view('branches.create'); 
    }

    /**
     * Handle the deployment form submission for a new branch.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'branch_name'    => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'branch_contact' => 'required|string|max:20',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $branch = Branch::create($validated);

            // Maintain complete compliance tracking sequence
            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'CREATE_BRANCH',
                'module'      => 'Branch Infrastructure',
                'description' => "Authorized new branch deployment: {$branch->branch_name} located at {$branch->location}.",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('branches.index')->with('success', 'Branch successfully deployed to the network.');
        });
    }

    /**
     * Show the edit form for an existing infrastructure node.
     */
    public function edit($id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::findOrFail($id);
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update the specifications of an operational branch.
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'branch_name'    => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'branch_contact' => 'required|string|max:255',
        ]);

        return DB::transaction(function () use ($branch, $validated, $request) {
            $branch->update($validated);

            // Log modifications to the infrastructure landscape
            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'UPDATE_BRANCH',
                'module'      => 'Branch Infrastructure',
                'description' => "Modified configurations for branch node: {$branch->branch_name}. Current address: {$branch->location}.",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
        });
    }

    /**
     * Toggle the active status flag of the operational branch.
     */
    public function toggle(Request $request, $id)
    {
        if (Auth::user()->role_id > 2) {
            abort(403, 'Unauthorized action.');
        }

        $branch = Branch::findOrFail($id);

        return DB::transaction(function () use ($branch, $request) {
            $branch->is_active = !$branch->is_active;
            $branch->save();

            $statusText = $branch->is_active ? 'Activated' : 'Deactivated';

            AuditTrail::create([
                'user_id'     => Auth::id(),
                'action'      => 'TOGGLE_BRANCH_STATUS',
                'module'      => 'Branch Infrastructure',
                'description' => "Branch infrastructure toggle flipped for: {$branch->branch_name}. Current status: {$statusText}",
                'ip_address'  => $request->ip(),
            ]);

            return redirect()->route('branches.index')->with('status', "Branch structural state updated to {$statusText}.");
        });
    }
}