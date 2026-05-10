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
    // Clean up the failed view first if it partially exists
    DB::statement("DROP VIEW IF EXISTS payroll_calculations");

    DB::statement("
        CREATE VIEW payroll_calculations AS
        SELECT 
            e.employee_id,
            e.full_name,
            e.employee_id_number,
            p.position_title,
            p.basic_pay,
            -- Calculate total hours from attendance_logs
            SUM(COALESCE(TIMESTAMPDIFF(SECOND, a.time_in, a.time_out), 0) / 3600) as total_hours_worked,
            -- Calculate Gross Pay (Hours * basic_pay)
            (SUM(COALESCE(TIMESTAMPDIFF(SECOND, a.time_in, a.time_out), 0) / 3600) * p.basic_pay) as calculated_gross_pay
        FROM employees e
        JOIN positions p ON e.position_id = p.position_id
        LEFT JOIN attendance_logs a ON e.employee_id = a.employee_id
        WHERE a.status = 'Verified'
        GROUP BY e.employee_id, e.full_name, e.employee_id_number, p.position_title, p.basic_pay
    ");
}    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_calculation_view');
    }
};
