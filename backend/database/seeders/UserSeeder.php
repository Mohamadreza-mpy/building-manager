<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['mobile' => '09120000001'], [
            'name' => 'Admin User',
            'mobile' => '09120000001',
            'password' => '123456',
            'role' => 'admin',
        ]);

        User::updateOrCreate(['mobile' => '09120000002'], [
            'name' => 'Building Manager',
            'mobile' => '09120000002',
            'password' => '123456',
            'role' => 'manager',
        ]);

        User::updateOrCreate(['mobile' => '09120000003'], [
            'name' => 'Building Resident',
            'mobile' => '09120000003',
            'password' => '123456',
            'role' => 'resident',
        ]);
    }
}
