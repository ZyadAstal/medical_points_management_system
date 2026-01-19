<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PharmacistSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Pharmacist Ahmed',
            'username'          => 'pharmacist1',
            'email'             => 'pharmacist1@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 4, // Pharmacist in RoleSeeder order
            'medical_center_id' => 1,
        ]);
    }
}
