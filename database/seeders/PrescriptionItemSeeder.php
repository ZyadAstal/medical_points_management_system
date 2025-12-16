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
            foreach ($medicines as $medicine) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id'     => $medicine->id,
                    'quantity'        => rand(1, 3),
                ]);
            }
        });
    }
}
