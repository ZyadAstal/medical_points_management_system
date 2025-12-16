<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name'              => 'Patient User',
            'username'          => 'patient1',
            'email'             => 'patient1@example.com',
            'password'          => Hash::make('password'),
            'role_id'           => 5, // Patient
            'medical_center_id' => 1,
        ]);

        Patient::create([
            'user_id'     => $user->id,
            'full_name'   => 'Ahmed Ali',
            'national_id' => '123456789',
            'address'     => 'Gaza City',
            'phone'       => '0599123456',
            'points'      => 100,
        ]);
    }
}
