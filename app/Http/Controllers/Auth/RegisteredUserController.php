<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use App\Models\Position;
use App\Models\AuditTrail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
{
    // 1. Validate the incoming input fields from your clean registration view
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // 2. Wrap operations inside a database transaction to prevent partial/broken records
    return DB::transaction(function () use ($request) {
        
        $isFirstUser = User::count() === 0;
        $roleId = $isFirstUser ? 1 : 2; // Role 1: Admin | Role 2: Employee

        // 3. FIX: Add 'name' here so the 1364 General Error disappears completely
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $roleId,
            'password' => Hash::make($request->password),
        ]);

        // 4. Locate the baseline "Trainee" position configuration record
        $traineePosition = Position::where('position_title', 'Trainee')
            ->orWhere('position_title', 'like', '%trainee%')
            ->first();

        // 5. AUTOMATION: Create the linked Employee Profile record right away
        // This instantly populates your empty Scorecard and Payroll dropdown selectors!
        Employee::create([
            'user_id'     => $user->user_id, // Uses your custom 3NF Primary Key
            'full_name'   => $user->name,
            'position_id' => $traineePosition ? $traineePosition->position_id : null,
            'branch_id'   => null, // Left unassigned until modified by a manager
        ]);

        // 6. Push system telemetry footprints directly into your Security Audit Trail
        AuditTrail::create([
            'user_id'     => $user->user_id,
            'action'      => 'USER_SELF_REGISTER',
            'module'      => 'Authentication Module',
            'description' => "System terminal initialized for: {$user->email}. Linked profile established with auto-assigned Trainee baseline.",
            'ip_address'  => $request->ip(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    });
}
}