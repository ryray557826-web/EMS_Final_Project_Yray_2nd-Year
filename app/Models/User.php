<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    // Use the custom Primary Key from your 3NF structure
    protected $primaryKey = 'user_id';

    protected $fillable = [
    'name',      // Ensure this is here
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