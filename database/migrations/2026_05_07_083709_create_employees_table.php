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
    Schema::create('employees', function (Blueprint $table) {
        $table->id('employee_id');
        $table->foreignId('user_id')->constrained('users', 'user_id')->onDelete('cascade');
        $table->foreignId('position_id')->constrained('positions', 'position_id');
        
        // MAKE SURE THIS LINE EXISTS:
        $table->foreignId('branch_id')->constrained('branches', 'branch_id');
        
        $table->string('full_name');
        $table->string('employee_id_number')->unique();
        $table->string('status')->default('Active');
        $table->date('hire_date')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
