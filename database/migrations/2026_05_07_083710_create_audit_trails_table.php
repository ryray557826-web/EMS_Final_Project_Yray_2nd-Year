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
    Schema::create('audit_trails', function (Blueprint $table) {
    $table->id('audit_id');
    $table->foreignId('user_id')->constrained('users', 'user_id'); // Who did it
    $table->string('action'); // CREATE, UPDATE, DELETE
    $table->string('module'); // Employees, Branches, Positions
    $table->text('description'); // Detailed change log
    $table->ipAddress('ip_address')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
