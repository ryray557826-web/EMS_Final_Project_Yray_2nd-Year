<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            // New columns to store immutable finalized states
            $table->decimal('final_gross_pay', 10, 2)->nullable()->after('net_amount');
            $table->boolean('is_locked')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('payroll_transactions', function (Blueprint $table) {
            $table->dropColumn(['final_gross_pay', 'is_locked']);
        });
    }
};