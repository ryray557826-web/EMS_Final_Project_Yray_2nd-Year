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
    DB::unprepared("
        CREATE TRIGGER audit_salary_update
        AFTER UPDATE ON salary_profiles
        FOR EACH ROW
        BEGIN
            INSERT INTO audit_trails (user_id, table_name, old_values, new_values, created_at, updated_at)
            VALUES (
                (SELECT user_id FROM employees WHERE employee_id = NEW.employee_id),
                'salary_profiles',
                CONCAT('Rate: ', OLD.base_hourly_rate),
                CONCAT('Rate: ', NEW.base_hourly_rate),
                NOW(),
                NOW()
            );
        END;
    ");
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_audit_trigger');
    }
};
