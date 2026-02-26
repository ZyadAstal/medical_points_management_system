<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MedicalCenterSeeder::class,
            UserSeeder::class,
            ManagerSeeder::class,
            DoctorSeeder::class,
            PharmacistSeeder::class,
            ReceptionSeeder::class,
            MedicineSeeder::class,
            InventorySeeder::class,
            PatientSeeder::class,
            PrescriptionSeeder::class,
            PrescriptionItemSeeder::class,
            DispenseSeeder::class,
        ]);
    }
}
