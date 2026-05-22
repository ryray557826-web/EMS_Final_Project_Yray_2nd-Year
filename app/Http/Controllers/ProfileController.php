<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $oldEmail = $user->email;

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Security Audit: Log the profile update
        AuditTrail::create([
            'user_id' => $user->user_id,
            'action' => 'UPDATE',
            'module' => 'Profile',
            'description' => "User updated profile information. " . ($oldEmail !== $user->email ? "Email changed to {$user->email}" : ""),
            'ip_address' => $request->ip()
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Process allowance dynamic balance extraction routing.
     */
    public function cashout(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        // Relationship health validation fallback check
        if (!$user->employee || !$user->employee->salaryProfile) {
            return Redirect::route('profile.edit')->with('error', 'Salary framework configurations not found.');
        }

        $salaryProfile = $user->employee->salaryProfile;
        $amountToCashOut = $salaryProfile->total_allowance;

        if ($amountToCashOut <= 0) {
            return Redirect::route('profile.edit')->with('error', 'No extractable balance detected on employee record.');
        }

        // DB Transaction guarantees updates & audits commit together perfectly
        DB::transaction(function () use ($user, $salaryProfile, $amountToCashOut, $request) {
            // 1. Flush compensation framework ledger back down to zero baseline
            $salaryProfile->total_allowance = 0.00;
            $salaryProfile->save();

            // 2. Transmit historical audit logs track record
            AuditTrail::create([
                'user_id'     => $user->user_id,
                'action'      => 'CASHOUT',
                'module'      => 'Salary Profiles',
                'description' => "Employee self-cashed out accumulated total allowance matching value: ₱" . number_format($amountToCashOut, 2),
                'ip_address'  => $request->ip(),
            ]);
        });

        return Redirect::route('profile.edit')->with('status', 'Allowance payout extracted successfully. Corporate accounting ledger notified.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Audit before deletion
        AuditTrail::create([
            'user_id' => $user->user_id,
            'action' => 'DELETE',
            'module' => 'Profile',
            'description' => "User self-terminated account: {$user->email}",
            'ip_address' => $request->ip()
        ]);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}