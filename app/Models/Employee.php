<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'user_id',
        'employee_id_number',
        'full_name',
        'position_id',
        'branch_id',
        'status',
        'hire_date', // Added to match your migration
    ];

    // --- Relationships ---

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function salaryProfile()
    {
        return $this->hasOne(SalaryProfile::class, 'employee_id', 'employee_id');
    }

    // Added to support Attendance and KPI Business Rules
    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'employee_id', 'employee_id');
    }

    public function kpiScorecards()
    {
        return $this->hasMany(KpiScorecard::class, 'employee_id', 'employee_id');
    }
    
    public function payrollTransactions()
    {
        return $this->hasMany(PayrollTransaction::class, 'employee_id', 'employee_id');
    }
}