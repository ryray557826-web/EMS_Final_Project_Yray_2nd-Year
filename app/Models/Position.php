<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';
    protected $primaryKey = 'position_id'; 

    protected $fillable = [
        'position_title',
        'job_level',
        'hourly_rate',
        'role_id',
    ];

    public function employees() {
    return $this->hasMany(Employee::class, 'position_id', 'position_id');
}
}