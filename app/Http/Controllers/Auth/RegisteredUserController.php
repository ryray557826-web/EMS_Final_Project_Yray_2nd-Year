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
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Wrap the entire multi-table sequence inside a database transaction
        return DB::transaction(function () use ($request) {
            
            $isFirstUser = User::count() === 0;
            $roleId = $isFirstUser ? 1 : 2; // Role ID 1: Admin | Role ID 2: Employee

            // 1. Provision the primary User Authentication node
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'role_id'  => $roleId,
                'password' => Hash::make($request->password),
            ]);

            // 2. Locate the default "Trainee" infrastructure position framework
            $traineePosition = Position::where('position_title', 'Trainee')
                ->orWhere('position_title', 'like', '%trainee%')
                ->first();

            // 3. Auto-generate the linked Employee Profile node mapped to this user 
            Employee::create([
                'user_id'     => $user->user_id, // Links directly using your custom 3NF Primary Key
                'full_name'   => $user->name,
                'position_id' => $traineePosition ? $traineePosition->position_id : null, // Fallback gracefully if not seeded yet
                'branch_id'   => null, // Kept unassigned until authorized by system management
            ]);

            // 4. Telemetry Log: Commit registration footprint to audit records
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