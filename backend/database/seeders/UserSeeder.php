<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'mobile' => '09120000001',
            'password' => '123456',
            'role' => 'admin',
        ]);


        User::create([
            'name' => 'Building Manager',
            'mobile' => '09120000002',
            'password' => '123456',
            'role' => 'manager',
        ]);


        User::create([
            'name' => 'Building Resident',
            'mobile' => '09120000003',
            'password' => '123456',
            'role' => 'resident',
        ]);
    }
}
