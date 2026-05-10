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
    // 1. Clear the old view so we can create the fresh one
    DB::statement("DROP VIEW IF EXISTS branch_activity_logs");

    // 2. Create the view
    DB::statement("
        CREATE VIEW branch_activity_logs AS
        SELECT 
            u.name as employee_name,
            e.employee_id_number,
            DATE_FORMAT(a.time_in, '%h:%i %p') as time_display,
            'IN' as type,
            a.status
        FROM attendance_logs a
        JOIN employees e ON a.employee_id = e.employee_id
        JOIN users u ON e.user_id = u.user_id
        UNION ALL
        SELECT 
            u.name as employee_name,
            e.employee_id_number,
            DATE_FORMAT(a.time_out, '%h:%i %p') as time_display,
            'OUT' as type,
            a.status
        FROM attendance_logs a
        JOIN employees e ON a.employee_id = e.employee_id
        JOIN users u ON e.user_id = u.user_id
        WHERE a.time_out IS NOT NULL
        ORDER BY time_display DESC
    ");
}

public function down(): void
{
    DB::statement("DROP VIEW IF EXISTS branch_activity_logs");
}
};
