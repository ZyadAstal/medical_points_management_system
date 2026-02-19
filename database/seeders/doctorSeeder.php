<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'name'              => 'د. أحمد خالد محمود',
                'username'          => 'doctor1',
                'email'             => 'doctor1@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 3,
                'medical_center_id' => 1,
            ],
            [
                'name'              => 'د. سمير حسن أبو العلا',
                'username'          => 'doctor2',
                'email'             => 'doctor2@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 3,
                'medical_center_id' => 1,
            ],
            [
                'name'              => 'د. فاطمة عبد الرحمن نصر',
                'username'          => 'doctor3',
                'email'             => 'doctor3@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 3,
                'medical_center_id' => 2,
            ],
        ];

        foreach ($doctors as $doctor) {
            User::firstOrCreate(
                ['username' => $doctor['username']],
                $doctor
            );
        }
    }
}
