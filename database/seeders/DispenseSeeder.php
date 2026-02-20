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

        foreach ($prescriptionItems as $item) {
            if ($item->dispenses()->exists()) continue;

            $centerId = $item->prescription->doctor->medical_center_id ?? $item->prescription->patient->user->medical_center_id;
            
            // Find a pharmacist in this center
            $pharmacistId = null;
            if (isset($pharmacistsByCenter[$centerId]) && $pharmacistsByCenter[$centerId]->count() > 0) {
                $pharmacistId = $pharmacistsByCenter[$centerId]->random()->id;
            }

            $pointsUsed = $item->quantity * $item->medicine->points_cost;
            Dispense::create([
                'prescription_item_id' => $item->id,
                'medical_center_id'    => $centerId,
                'pharmacist_id'        => $pharmacistId,
                'quantity'             => $item->quantity,
                'points_used'          => $pointsUsed,
            ]);

            // Decrement points directly from patient associated with prescription
            $item->prescription->patient->decrement('points', $pointsUsed);
        }
    }
}
