<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name'              => 'مدير النظام',
                'username'          => 'admin',
                'email'             => 'admin@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 1, // SuperAdmin
                'medical_center_id' => 1,
            ]
        );
    }
}
