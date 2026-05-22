<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id'; // Correctly mapping to the default ID

    protected $fillable = ['name'];

    /**
     * Relationship: One Role has many Users.
     * Changed 'role_id' to 'id' to match the parent key in the roles table.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'role_id', 'id');
    }

    /**
     * Relationship: One Role has many Permissions.
     * Changed 'role_id' to 'id' to match the parent key.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}