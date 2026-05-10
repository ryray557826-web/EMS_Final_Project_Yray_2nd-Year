<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'employee_id';

    // app/Models/Employee.php

    protected $fillable = [
        'user_id',
        'position_id',
        'branch_id',
        'full_name',
        'employee_id_number', // Add this
        'status',
        'hire_date',
    ];

    public function user() { 
    return $this->belongsTo(User::class, 'user_id', 'user_id'); 
}

public function position() { 
    return $this->belongsTo(Position::class, 'position_id', 'position_id'); 
}

// ADD THIS METHOD:
public function branch() { 
    return $this->belongsTo(Branch::class, 'branch_id', 'branch_id'); 
}

// For your attendance logs logic:
public function attendanceLogs() {
    return $this->hasMany(AttendanceLog::class, 'employee_id', 'employee_id');
}
}