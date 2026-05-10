<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

    protected $primaryKey = 'position_id';

    protected $fillable = [
        'position_title',
        'job_level',
        'basic_pay',
    ];

    /**
     * Relationship: A position is held by many employees.
     */
    public function employees()
    {
        return $this->hasMany(Employee::class, 'position_id', 'position_id');
    }
}