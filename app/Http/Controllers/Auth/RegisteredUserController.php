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

    return DB::transaction(function () use ($request) {
        
        $isFirstUser = User::count() === 0;
        $roleId = $isFirstUser ? 1 : 2; // 1 = Admin, 2 = Employee

        // 1. Create the base User record
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $roleId,
            'password' => Hash::make($request->password),
        ]);

        // 2. Query for the Junior Employee record space
        $juniorPosition = Position::where('position_title', 'Junior Employee')
            ->orWhere('position_title', 'like', '%Junior%')
            ->first();

        // 3. DEFENSIVE GENERATION: Build the Junior Employee row if missing from table
        if (!$juniorPosition) {
            $juniorPosition = Position::create([
                'position_title' => 'Junior Employee',
                'job_level'      => 'Entry Level',
                'hourly_rate'    => 50.00, // Mapped close to your Junior Agent baseline architecture
                'is_active'      => 1,
                'role_id'        => 2, 
            ]);
        }

        // 4. Attach the Employee Profile row assigning branch_id to 1 (Main Office)
        Employee::create([
            'user_id'     => $user->user_id, 
            'full_name'   => $user->name,
            'position_id' => $juniorPosition->position_id, 
            'branch_id'   => 1, // FIXED: Explicitly assigns to Main Office to pass NOT NULL validation
        ]);

        // 5. System Footprint Logging
        AuditTrail::create([
            'user_id'     => $user->user_id,
            'action'      => 'USER_SELF_REGISTER',
            'module'      => 'Authentication Module',
            'description' => "Account initialized for: {$user->email}. Automatically mapped to Junior Employee position under Main Office (Branch 1).",
            'ip_address'  => $request->ip(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    });
}
}