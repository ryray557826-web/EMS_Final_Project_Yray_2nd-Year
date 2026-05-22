<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::unprepared("
            CREATE TRIGGER sync_position_rate_to_salary
            BEFORE INSERT ON salary_profiles
            FOR EACH ROW
            BEGIN
                -- Inherit the rate from the positions table based on the assigned position_id
                SET NEW.base_hourly_rate = (SELECT hourly_rate FROM positions WHERE position_id = NEW.position_id);
            END;
        ");
    }

    public function down(): void {
        DB::unprepared("DROP TRIGGER IF EXISTS sync_position_rate_to_salary");
    }
};