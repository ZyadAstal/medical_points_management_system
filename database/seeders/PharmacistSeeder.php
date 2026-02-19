<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PharmacistSeeder extends Seeder
{
    public function run(): void
    {
        $pharmacists = [
            [
                'name'              => 'صيدلي أحمد محمود يوسف',
                'username'          => 'pharmacist1',
                'email'             => 'pharmacist1@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 4,
                'medical_center_id' => 1,
            ],
            [
                'name'              => 'صيدلية فاطمة خالد عمر',
                'username'          => 'pharmacist2',
                'email'             => 'pharmacist2@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 4,
                'medical_center_id' => 2,
            ],
            [
                'name'              => 'صيدلي محمد إبراهيم حسن',
                'username'          => 'pharmacist3',
                'email'             => 'pharmacist3@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 4,
                'medical_center_id' => 3,
            ],
            [
                'name'              => 'صيدلية ليلى محمود علي',
                'username'          => 'pharmacist4',
                'email'             => 'pharmacist4@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => 4,
                'medical_center_id' => 4,
            ],
        ];

        foreach ($pharmacists as $pharmacist) {
            User::firstOrCreate(
                ['username' => $pharmacist['username']],
                $pharmacist
            );
        }
    }
}
