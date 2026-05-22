<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollAdjustmentRequest extends Model
{
    // Ensure this matches your migration table name
    protected $table = 'payroll_adjustment_requests'; 
    protected $primaryKey = 'adjustment_id'; 
    
    protected $fillable = [
        'transaction_id', 
        'employee_id', 
        'requested_adjustment', 
        'reason', 
        'status'
    ];
}