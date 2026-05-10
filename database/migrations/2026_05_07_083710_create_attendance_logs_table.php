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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id('attendance_id'); // Primary Key
            
            // Foreign Key to Employees table
            $table->unsignedBigInteger('employee_id');
            
            // Foreign Key to Branches table
            $table->unsignedBigInteger('branch_id');
            
            // Time tracking
            $table->timestamp('time_in')->nullable();
            $table->timestamp('time_out')->nullable();
            
            // Status for verification (Matches your Controller logic)
            $table->string('status')->default('Verified'); 
            
            // Geo-location or IP tracking (Optional but recommended for BPO systems)
            $table->string('ip_address')->nullable();
            
            $table->timestamps();

            // Set up Foreign Key Constraints
            $table->foreign('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreign('branch_id')->references('branch_id')->on('branches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};