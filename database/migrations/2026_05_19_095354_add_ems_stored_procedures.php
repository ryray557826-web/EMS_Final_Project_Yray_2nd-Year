<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Ensure no leftover procedure exists
        DB::unprepared("DROP PROCEDURE IF EXISTS ProcessPayrollWithTransaction");
        
        // Execute the procedure creation
        DB::unprepared("
            CREATE PROCEDURE ProcessPayrollWithTransaction(
                IN p_emp_id BIGINT UNSIGNED,
                IN p_gross DECIMAL(10,2),
                IN p_bonus DECIMAL(10,2),
                IN p_deduct DECIMAL(10,2),
                IN p_net DECIMAL(10,2),
                IN p_ref VARCHAR(50),
                IN p_start DATE,
                IN p_end DATE,
                IN p_user_id BIGINT UNSIGNED,
                IN p_ip VARCHAR(45)
            )
            BEGIN
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    ROLLBACK;
                    RESIGNAL;
                END;

                START TRANSACTION;
                
                INSERT INTO payroll_transactions (
                    employee_id, gross_amount, bonus_amount, deductions, 
                    net_amount, reference_number, pay_period_start, 
                    pay_period_end, status, is_locked, created_at, updated_at
                ) VALUES (
                    p_emp_id, p_gross, p_bonus, p_deduct, 
                    p_net, p_ref, p_start, 
                    p_end, 'Processed', 0, NOW(), NOW()
                );
                
                INSERT INTO audit_trails (
                    user_id, action, module, description, ip_address, created_at, updated_at
                ) VALUES (
                    p_user_id, 'CREATE', 'Payroll', 
                    CONCAT('System initialized raw ledger runtime transaction: ', p_ref), 
                    p_ip, NOW(), NOW()
                );

                COMMIT;
            END
        ");
    }

    public function down(): void {
        DB::unprepared("DROP PROCEDURE IF EXISTS ProcessPayrollWithTransaction");
    }
};