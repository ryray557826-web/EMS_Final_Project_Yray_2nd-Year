<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Position;
use App\Models\Employee;
use App\Models\SalaryProfile;
use App\Models\PayrollTransaction;
use App\Models\PayrollAdjustmentRequest;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Setup Roles
        Role::create(['name' => 'Super Admin']); // ID 1
        Role::create(['name' => 'Admin']);       // ID 2
        Role::create(['name' => 'User']);        // ID 3

        // 2. Create Branches
        Branch::create(['branch_id' => 1, 'branch_name' => 'Main Office', 'location' => 'Davao City', 'branch_contact' => '082-123-4567']);
        Branch::create(['branch_id' => 2, 'branch_name' => 'North Branch', 'location' => 'Panabo City', 'branch_contact' => '084-987-6543']);

        // 3. Create Positions
        Position::create(['position_id' => 1, 'position_title' => 'System Architect', 'job_level' => 'Senior', 'hourly_rate' => 500.00, 'role_id' => 1]);
        Position::create(['position_id' => 2, 'position_title' => 'Branch Manager', 'job_level' => 'Mid', 'hourly_rate' => 300.00, 'role_id' => 2]);
        Position::create(['position_id' => 3, 'position_title' => 'Junior Staff', 'job_level' => 'Entry', 'hourly_rate' => 75.00, 'role_id' => 3]);

        // 4. Create Users and Employees
        $users = [
            ['name' => 'Super Admin', 'email' => 'admin@system.com', 'role_id' => 1, 'pos' => 1, 'branch' => 1],
            ['name' => 'Branch Manager', 'email' => 'manager@branch.com', 'role_id' => 2, 'pos' => 2, 'branch' => 2],
            ['name' => 'John Doe', 'email' => 'john@staff.com', 'role_id' => 3, 'pos' => 3, 'branch' => 2],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'role_id' => $userData['role_id'],
                'email_verified_at' => now(), // Auto-verify for testing
            ]);

            $employee = Employee::create([
                'user_id' => $user->user_id,
                'employee_id_number' => 'EMP-' . rand(1000, 9999),
                'full_name' => $userData['name'],
                'position_id' => $userData['pos'],
                'branch_id' => $userData['branch'],
                'status' => 'Active'
            ]);

            SalaryProfile::create([
                'employee_id' => $employee->employee_id,
                'position_id' => $userData['pos'],
                'total_allowance' => 1000.00
            ]);
        }

        // 5. Sample Transaction for Demo
        $transaction = PayrollTransaction::create([
            'employee_id' => 3, // John Doe
            'pay_period_start' => now()->startOfMonth(),
            'pay_period_end' => now()->endOfMonth(),
            'gross_amount' => 15000.00,
            'net_amount' => 14500.00,
            'reference_number' => 'REF-001',
            'status' => 'Processed'
        ]);

        PayrollAdjustmentRequest::create([
            'transaction_id' => $transaction->transaction_id,
            'employee_id' => 3,
            'requested_adjustment' => 500.00,
            'reason' => 'Performance Bonus',
            'status' => 'Pending'
        ]);
    }
}