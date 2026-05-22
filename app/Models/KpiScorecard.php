<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiScorecard extends Model
{
    use HasFactory;

    // Explicitly target the custom primary key
    protected $primaryKey = 'kpi_id';

    protected $fillable = [
        'employee_id',
        'evaluation_score',
        'remarks',
    ];

    /**
     * Relationship: A scorecard belongs to a specific employee profile.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}