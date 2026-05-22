<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the view if it exists to ensure a clean slate
        DB::unprepared("DROP VIEW IF EXISTS payroll_calculations");

        // Create the view pulling from salary_profiles for more accurate rate tracking
        DB::unprepared("
            CREATE VIEW payroll_calculations AS
            SELECT 
                e.employee_id,
                e.full_name,
                e.employee_id_number,
                p.position_title,
                sp.base_hourly_rate as basic_pay, 
                SUM(COALESCE(TIMESTAMPDIFF(SECOND, a.time_in, a.time_out), 0) / 3600) as total_hours_worked,
                (SUM(COALESCE(TIMESTAMPDIFF(SECOND, a.time_in, a.time_out), 0) / 3600) * sp.base_hourly_rate) as calculated_gross_pay
            FROM employees e
            JOIN positions p ON e.position_id = p.position_id
            JOIN salary_profiles sp ON e.employee_id = sp.employee_id
            LEFT JOIN attendance_logs a ON e.employee_id = a.employee_id
            WHERE a.status = 'Verified'
            GROUP BY 
                e.employee_id, 
                e.full_name, 
                e.employee_id_number, 
                p.position_title, 
                sp.base_hourly_rate
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP VIEW IF EXISTS payroll_calculations");
    }
};