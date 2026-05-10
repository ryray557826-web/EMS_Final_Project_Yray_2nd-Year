<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    // Define the custom primary key
    protected $primaryKey = 'role_id';

    // Allow mass assignment for these fields
    protected $fillable = [
        'role_id',
        'role_name',
    ];

    /**
     * Relationship: One Role has many Users.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'role_id');
    }

    /**
     * Relationship: One Role has many Permissions (Pivot).
     * Only keep this if you are still using the role_permission table.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}