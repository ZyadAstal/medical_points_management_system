<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrescriptionItem;
use App\Models\Dispense;

use App\Models\User;

use App\Models\Role;

class DispenseSeeder extends Seeder
{
    public function run(): void
    {
        $pharmacistRoleId = Role::where('name', 'Pharmacist')->first()?->id ?? 4;
        $pharmacistsByCenter = User::where('role_id', $pharmacistRoleId)
            ->get()
            ->groupBy('medical_center_id');

        $prescriptions = \App\Models\Prescription::with('items.medicine', 'patient')->get();

        foreach ($prescriptions as $prescription) {
            $items = $prescription->items->where('is_dispensed', false);
            if ($items->isEmpty()) continue;

            $dispenseType = rand(1, 4); // 1: Full, 2: Partial, 3,4: None (to keep some pending)
            if ($dispenseType >= 3) continue; 

            $itemsToDispense = $dispenseType == 1 ? $items : $items->random(max(1, intval($items->count() / 2)));

            foreach ($itemsToDispense as $item) {
                $centerId = $prescription->doctor->medical_center_id ?? null;
                if (!$centerId) continue;
                
                $pharmacistId = null;
                if (isset($pharmacistsByCenter[$centerId]) && $pharmacistsByCenter[$centerId]->count() > 0) {
                    $pharmacistId = $pharmacistsByCenter[$centerId]->random()->id;
                }

                $pointsUsed = $item->quantity * $item->medicine->points_cost;

                // Check Patient Balance before seeding a dispense
                // For seeding realistic data, if they don't have enough, we just skip dispensing this item
                if ($prescription->patient->points >= $pointsUsed && $pharmacistId) {
                    Dispense::create([
                        'prescription_item_id' => $item->id,
                        'medical_center_id'    => $centerId,
                        'pharmacist_id'        => $pharmacistId,
                        'quantity'             => $item->quantity,
                        'points_used'          => $pointsUsed,
                        'created_at'           => $prescription->created_at->addMinutes(rand(10, 120)),
                    ]);

                    $item->update(['is_dispensed' => true]);
                    $prescription->patient->decrement('points', $pointsUsed);
                }
            }
        }
    }
}
