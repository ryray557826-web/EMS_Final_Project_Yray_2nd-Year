<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payroll_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->foreignId('employee_id')->constrained('employees', 'employee_id');
            
            // Financial Details
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('bonus_amount', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2);
            
            // Tracking
            $table->string('reference_number')->unique();
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->string('status')->default('Processed');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_transactions');
    }
};