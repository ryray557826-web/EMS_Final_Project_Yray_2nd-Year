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
        $table->string('position_title'); // e.g., Web Developer, Manager, Agent
        $table->string('job_level');      // e.g., Entry, Junior, Senior
        $table->decimal('basic_pay', 10, 2); // 10 digits total, 2 after decimal
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
