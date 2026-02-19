<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;

class ManagerSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::where('name', 'CenterManager')->first();

        if ($role) {
            $managers = [
                [
                    'name'              => 'ماجد سعيد أبو عمرة',
                    'username'          => 'manager1',
                    'email'             => 'manager1@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 1,
                ],
                [
                    'name'              => 'عبير خالد حسونة',
                    'username'          => 'manager2',
                    'email'             => 'manager2@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 2,
                ],
                [
                    'name'              => 'طارق محمد الحاج',
                    'username'          => 'manager3',
                    'email'             => 'manager3@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 3,
                ],
                [
                    'name'              => 'هناء عادل البطش',
                    'username'          => 'manager4',
                    'email'             => 'manager4@example.com',
                    'password'          => Hash::make('password'),
                    'role_id'           => $role->id,
                    'medical_center_id' => 4,
                ],
            ];

            foreach ($managers as $manager) {
                User::firstOrCreate(
                    ['username' => $manager['username']],
                    $manager
                );
            }
        }
    }
}
