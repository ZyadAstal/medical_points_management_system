<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        Medicine::create([
            'name'        => 'Paracetamol',
            'points_cost' => 5,
            'expiry_date' => '2026-12-31',
        ]);

        Medicine::create([
            'name'        => 'Ibuprofen',
            'points_cost' => 8,
            'expiry_date' => '2026-10-15',
        ]);

        Medicine::create([
            'name'        => 'Amoxicillin',
            'points_cost' => 12,
            'expiry_date' => '2026-08-01',
        ]);
    }
}
