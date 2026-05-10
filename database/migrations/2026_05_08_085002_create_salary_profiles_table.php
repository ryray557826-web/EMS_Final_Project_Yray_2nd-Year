<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('salary_profiles', function (Blueprint $table) {
        $table->id('salary_id');
        $table->foreignId('employee_id')->constrained('employees', 'employee_id')->onDelete('cascade');
        $table->decimal('base_hourly_rate', 10, 2)->default(0.00); // Pulled from Position initially
        $table->decimal('total_allowance', 10, 2)->default(0.00);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_profiles');
    }
};
