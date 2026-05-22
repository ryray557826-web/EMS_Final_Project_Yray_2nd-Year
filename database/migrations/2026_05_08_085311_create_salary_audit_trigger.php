<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop existing trigger to avoid conflicts
        DB::unprepared("DROP TRIGGER IF EXISTS after_salary_profile_update");

        // 2. Create the updated audit trigger
        DB::unprepared("
            CREATE TRIGGER after_salary_profile_update
            AFTER UPDATE ON salary_profiles
            FOR EACH ROW    
            BEGIN
                -- Only log if the compensation fields actually change
                IF OLD.base_hourly_rate <> NEW.base_hourly_rate OR OLD.total_allowance <> NEW.total_allowance THEN
                    INSERT INTO audit_trails (
                        user_id,
                        action,
                        module,
                        description,
                        ip_address,
                        created_at,
                        updated_at
                    ) VALUES (
                        1, -- Placeholder for system or current user ID
                        'UPDATE',
                        'Salary Profiles',
                        CONCAT(
                            'Salary Profile Update for Employee ID: ', OLD.employee_id, 
                            ' | Rate: ', OLD.base_hourly_rate, ' -> ', NEW.base_hourly_rate,
                            ' | Allowance: ', OLD.total_allowance, ' -> ', NEW.total_allowance
                        ),
                        '127.0.0.1',
                        NOW(),
                        NOW()
                    );
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_salary_profile_update");
    }
};