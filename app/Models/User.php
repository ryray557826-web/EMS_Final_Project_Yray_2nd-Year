<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Use the custom Primary Key from your 3NF structure
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user has verified their email address.
     * Force true for the Super Admin based on your seeder email.
     */
    public function hasVerifiedEmail()
    {
        if ($this->email === 'r.yray.557826@umindanao.edu.ph') {
            return true;
        }

        return !is_null($this->email_verified_at);
    }

    /**
     * Relationship: A user can perform many actions recorded in the audit trail.
     */
    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class, 'user_id', 'user_id');
    }

    /**
     * Relationship: Link to the Employee Profile
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'user_id');
    }
}