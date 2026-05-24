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

        // 2. Query for the Trainee record space
        $traineePosition = Position::where('position_title', 'Trainee')->first();

        // 3. DEFENSIVE GENERATION: Build the Trainee row with all required columns if missing
        if (!$traineePosition) {
            $traineePosition = Position::create([
                'position_title' => 'Trainee',
                'job_level'      => 'Entry Level',
                'hourly_rate'    => 0.00,
                'is_active'      => 1,
                'role_id'        => 2, // Assigns default employee structural role context
            ]);
        }

        // 4. Attach the Employee Profile row safely
        Employee::create([
            'user_id'     => $user->user_id, 
            'full_name'   => $user->name,
            'position_id' => $traineePosition->position_id, 
            'branch_id'   => null, 
        ]);

        // 5. System Footprint Logging
        AuditTrail::create([
            'user_id'     => $user->user_id,
            'action'      => 'USER_SELF_REGISTER',
            'module'      => 'Authentication Module',
            'description' => "Account initialized for: {$user->email}. Automatically mapped to structural Trainee position profile.",
            'ip_address'  => $request->ip(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    });
}
}