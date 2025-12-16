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
            DoctorSeeder::class,
            PatientSeeder::class,
            MedicineSeeder::class,
            PrescriptionSeeder::class,
            PrescriptionItemSeeder::class,
            DispenseSeeder::class,
        ]);
    }
}
