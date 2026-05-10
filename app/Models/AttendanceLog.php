<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    use HasFactory;

    // Tell Laravel the actual name of your primary key
    protected $primaryKey = 'attendance_id';

    // Ensure mass assignment is allowed for these fields
    protected $fillable = [
        'employee_id',
        'branch_id',
        'time_in',
        'time_out',
        'status',
    ];

    /**
     * Get the employee associated with the log.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}