<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrescriptionItem;
use App\Models\Dispense;

class DispenseSeeder extends Seeder
{
    public function run(): void
    {
        $prescriptionItems = PrescriptionItem::all();

        foreach ($prescriptionItems as $item) {
            Dispense::create([
                'prescription_item_id' => $item->id,
                'medical_center_id'    => $item->prescription->patient->user->medical_center_id,
                'quantity'             => $item->quantity,
                'points_used'          => $item->quantity * $item->medicine->points_cost,
            ]);
        }
    }
}
