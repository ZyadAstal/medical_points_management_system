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
        // Ensure CenterManager role exists
        $role = Role::where('name', 'CenterManager')->first();
        
        if ($role) {
            User::create([
                'name'              => 'Manager Maged',
                'username'          => 'manager1',
                'email'             => 'manager1@example.com',
                'password'          => Hash::make('password'),
                'role_id'           => $role->id,
                'medical_center_id' => 1, // Assign to the first medical center
            ]);
        }
    }
}
