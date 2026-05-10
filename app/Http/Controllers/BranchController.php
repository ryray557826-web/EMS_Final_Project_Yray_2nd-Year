<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\AuditTrail; // Don't forget this for the audit logic
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchController extends Controller
{
    // 1. Show the list of branches
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('branches.index', compact('branches'));
    }

    // 2. Show the form to create a new branch
    public function create()
    {
        return view('branches.create'); 
    }

    // 3. Handle the form submission
    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name'    => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'branch_contact' => 'required|string|max:20',
        ]);

        $branch = Branch::create($validated);

        // Security Audit (This will work once AuditTrail $fillable is fixed!)
        AuditTrail::create([
            'user_id'     => Auth::id(),
            'action'      => 'CREATE',
            'module'      => 'Branches',
            'description' => "Authorized new branch: {$branch->branch_name} at {$branch->location}"
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch successfully deployed to the network.');
    }

    // 4. Show the edit form
    public function edit(Branch $branch)
    {
        return view('branches.create', compact('branch')); // Reusing the same view
    }
}