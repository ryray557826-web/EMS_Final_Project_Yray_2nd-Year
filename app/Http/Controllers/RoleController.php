<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display the list of roles (Read-only for Super Admin).
     */
    public function index()
    {
        if (Auth::user()->role_id !== 1) {
            abort(403);
        }

        $roles = Role::all();
        // You could create a simple read-only list here if needed
        return view('roles.index', compact('roles'));
    }

    /**
     * Optional: Logic to log when roles are assigned
     */
    public static function logRoleChange($targetUser, $oldRole, $newRole)
    {
        AuditTrail::create([
            'user_id' => Auth::id(),
            'action' => 'UPDATE',
            'module' => 'Roles',
            'description' => "Changed role for {$targetUser->name} from {$oldRole} to {$newRole}"
        ]);
    }
}