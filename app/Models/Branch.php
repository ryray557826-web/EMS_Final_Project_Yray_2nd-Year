<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;

    protected $primaryKey = 'branch_id';

    protected $fillable = [
        'branch_name',
        'location',
        'branch_contact',
    ];

    /**
     * Relationship: A branch has many attendance logs.
     */
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'branch_id', 'branch_id');
    }
}