<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Position;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    public function run(): void
{
    // 1. Create the Basic 3 Roles
    Role::create(['role_id' => 1, 'role_name' => 'Super Admin']);
    Role::create(['role_id' => 2, 'role_name' => 'Admin']);
    Role::create(['role_id' => 3, 'role_name' => 'User']);

    // 2. Create the default Super Admin (Only one call, with name)
    User::create([
        'name' => 'Super Admin', 
        'email' => 'r.yray.557826@umindanao.edu.ph',
        'password' => Hash::make('password123'), // Use your preferred password
        'role_id' => 1,
    ]);

    // 3. Create initial Branch
    Branch::create([
        'branch_id' => 1,
        'branch_name' => 'Main Office',
        'location' => 'Davao City', 
        'branch_contact' => '082-123-4567'
    ]);

    // 4. Create initial Position
    Position::create([
        'position_id' => 1,
        'position_title' => 'Trainee',
        'job_level' => 'Entry',
        'basic_pay' => 450.00 
    ]);
}
}