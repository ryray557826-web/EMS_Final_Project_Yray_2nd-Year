<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryProfileController extends Controller
{
    public function index()
    {
        // Enforce strict Super Admin restriction
        if (Auth::user()->role_id != 1) abort(403);

        // Fetch employees with their related position and salary profile records
        $employees = Employee::with(['position', 'salaryProfile'])->get();
        
        return view('salary-profiles.index', compact('employees'));
    }
}