<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class ReceptionSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'Reception')->first();

        if ($role) {
            $receptionists = [
                [
                    'name'              => 'ريم سامي أبو العينين',
                    'username'          => 'reception1',
                    'email'             => 'reception1@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 1,
                ],
                [
                    'name'              => 'نسرين وائل الغندور',
                    'username'          => 'reception2',
                    'email'             => 'reception2@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 2,
                ],
                [
                    'name'              => 'عمر فادي أبو سعدة',
                    'username'          => 'reception3',
                    'email'             => 'reception3@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 3,
                ],
            ];

            foreach ($receptionists as $receptionist) {
                User::firstOrCreate(
                    ['username' => $receptionist['username']],
                    $receptionist
                );
            }
        }
    }
}
