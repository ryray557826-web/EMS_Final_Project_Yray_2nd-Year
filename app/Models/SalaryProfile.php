<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryProfile extends Model
{
    use HasFactory;

    protected $primaryKey = 'salary_id';

    protected $fillable = [
        'employee_id',
        'position_id', // Added for trigger logic
        'base_hourly_rate',
        'total_allowance',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }
}