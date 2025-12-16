<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'              => 'Dr. Samir',
            'username'          => 'doctor1',
            'email'             => 'doctor1@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 4, // Doctor
            'medical_center_id' => 1,
        ]);
    }
}
