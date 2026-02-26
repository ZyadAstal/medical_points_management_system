<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Medicine;

class PrescriptionItemSeeder extends Seeder
{
    public function run(): void
    {
        $medicines = Medicine::all();

        Prescription::all()->each(function ($prescription) use ($medicines) {
            $randomMedicines = $medicines->random(rand(2, 5));
            foreach ($randomMedicines as $medicine) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id'     => $medicine->id,
                    'quantity'        => rand(1, 3),
                    'is_dispensed'    => false, // Default to false
                ]);
            }
        });
    }
}
