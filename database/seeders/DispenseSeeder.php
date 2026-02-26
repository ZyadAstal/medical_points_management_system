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
        $prescriptionItems = PrescriptionItem::all();

        // Get pharmacists grouped by medical_center_id
        $pharmacistRoleId = Role::where('name', 'Pharmacist')->first()?->id ?? 4;
        $pharmacistsByCenter = User::where('role_id', $pharmacistRoleId)
            ->get()
            ->groupBy('medical_center_id');

        $prescriptions = \App\Models\Prescription::all();

        foreach ($prescriptions as $prescription) {
            $items = $prescription->items;
            $dispenseType = rand(1, 4); // 1: Full, 2: Partial, 3,4: None (to keep some pending)

            if ($dispenseType >= 3) continue; 

            $itemsToDispense = ($dispenseType == 1 ? $items : $items->random(rand(1, count($items) - 1)))
                                ->where('is_dispensed', false);

            foreach ($itemsToDispense as $item) {
                // Determine Medical Center
                $centerId = $prescription->medical_center_id ?? 
                           ($prescription->doctor->medical_center_id ?? 
                            $prescription->patient->user->medical_center_id);
                
                // Find a pharmacist in this center
                $pharmacistId = null;
                if (isset($pharmacistsByCenter[$centerId]) && $pharmacistsByCenter[$centerId]->count() > 0) {
                    $pharmacistId = $pharmacistsByCenter[$centerId]->random()->id;
                }

                $pointsUsed = $item->quantity * $item->medicine->points_cost;

                // CRITICAL: Check Patient Balance before seeding a dispense
                if ($prescription->patient->points >= $pointsUsed) {
                    Dispense::create([
                        'prescription_item_id' => $item->id,
                        'medical_center_id'    => $centerId,
                        'pharmacist_id'        => $pharmacistId,
                        'quantity'             => $item->quantity,
                        'points_used'          => $pointsUsed,
                        'created_at'           => $prescription->created_at->addHours(rand(1, 24)),
                    ]);

                    // Update is_dispensed flag
                    $item->update(['is_dispensed' => true]);

                    // Decrement points directly from patient
                    $prescription->patient->decrement('points', $pointsUsed);
                }
            }
        }
    }
}
