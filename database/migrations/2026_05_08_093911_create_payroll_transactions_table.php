<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // 1. Create the Payroll Transactions Ledger
        Schema::create('payroll_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->decimal('gross_amount', 10, 2);
            $table->decimal('bonus_amount', 10, 2)->default(0.00);
            $table->decimal('deductions', 10, 2)->default(0.00);
            $table->decimal('net_amount', 10, 2);
            $table->string('reference_number')->unique();
            $table->date('pay_period_start');
            $table->date('pay_period_end');
            $table->enum('status', ['Processed', 'Pending Approval', 'Rejected', 'Rolled Back'])->default('Processed');
            $table->timestamps();
        });

        // 2. Create the Adjustment Requests Table (Must be created after payroll_transactions)
        Schema::create('payroll_adjustment_requests', function (Blueprint $table) {
            $table->id('adjustment_id');
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('requested_adjustment', 10, 2);
            $table->text('reason');
            $table->string('status')->default('Pending');
            $table->timestamps();

            $table->foreign('transaction_id')->references('transaction_id')->on('payroll_transactions')->onDelete('cascade');
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('payroll_adjustment_requests');
        Schema::dropIfExists('payroll_transactions');
    }
};