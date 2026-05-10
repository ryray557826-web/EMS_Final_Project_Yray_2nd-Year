<?php

namespace App\Http\Controllers; // THIS MUST BE CONTROLLERS

use App\Models\AuditTrail; // Import the model here
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditTrailController extends Controller
{
    public function index()
    {
        if (Auth::user()->role_id !== 1) {
            abort(403);
        }

        $logs = AuditTrail::with('user')->latest()->paginate(20);
        return view('audit.index', compact('logs'));
    }
}