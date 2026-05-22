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
        Schema::create('kpi_scorecards', function (Blueprint $table) {
    $table->id('kpi_id');
    $table->foreignId('employee_id')->constrained('employees', 'employee_id');
    $table->decimal('evaluation_score', 5, 2);
    $table->text('remarks')->nullable(); // Add this line
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_scorecards');
    }
};
