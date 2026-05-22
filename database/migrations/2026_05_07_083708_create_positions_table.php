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
Schema::create('positions', function (Blueprint $table) {
    $table->id('position_id');
    $table->string('position_title'); 
    $table->string('job_level');      
    $table->decimal('hourly_rate', 10, 2);
    
    // THIS IS THE FIX:
    // It creates an unsignedBigInteger and sets up the constraint correctly
    $table->foreignId('role_id')->default(3)->constrained('roles', 'id')->onDelete('cascade');
    
    $table->timestamps();    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
