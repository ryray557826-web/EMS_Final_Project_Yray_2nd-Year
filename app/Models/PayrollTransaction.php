<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollTransaction extends Model {
    protected $table = 'payroll_transactions';
    protected $primaryKey = 'transaction_id';

protected $fillable = [
    'employee_id', 
    'gross_amount', 
    'bonus_amount', 
    'deductions', 
    'net_amount', 
    'reference_number', 
    'pay_period_start', 
    'pay_period_end', 
    'status'
];

    public function employee(): BelongsTo {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}