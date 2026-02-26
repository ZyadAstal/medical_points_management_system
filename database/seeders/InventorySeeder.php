<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicalCenter;
use App\Models\Medicine;
use App\Models\Inventory;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $centers = MedicalCenter::all();
        $medicines = Medicine::all();

        if ($centers->isEmpty() || $medicines->isEmpty()) {
            return;
        }

        foreach ($centers as $center) {
            // Pick a random number of medicines for this center (e.g., 60% to 100% of available meds)
            $count = rand(ceil($medicines->count() * 0.6), $medicines->count());
            $selectedMeds = $medicines->random($count);

            foreach ($selectedMeds as $med) {
                // Random quantity between 0 and 50
                // We give a slightly higher chance for non-zero to make it playable
                $quantity = rand(0, 10) === 0 ? 0 : rand(5, 50);

                Inventory::updateOrCreate(
                    [
                        'medical_center_id' => $center->id,
                        'medicine_id'       => $med->id,
                    ],
                    [
                        'quantity' => $quantity,
                    ]
                );
            }
        }
    }
}
