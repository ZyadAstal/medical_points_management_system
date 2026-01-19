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
        // Ensure Reception role exists (it should be id 6 from RoleSeeder)
        $role = Role::where('name', 'Reception')->first();
        
        if ($role) {
            User::create([
                'name'              => 'Receptionist Reem',
                'username'          => 'reception1',
                'email'             => 'reception1@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $role->id,
                'medical_center_id' => 1,
            ]);
        }
    }
}
