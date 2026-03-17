<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prescription;
use App\Models\Patient;
use App\Models\User;

class PrescriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Get all completed visits
        $visits = \App\Models\Visit::where('status', \App\Models\Visit::STATUS_COMPLETED)->get();
        $notes = ['زيارة دورية', 'متابعة حالة', 'مراجعة نتائج تحاليل', 'فحص عام'];

        foreach ($visits as $visit) {
            // Give 70% of completed visits a prescription
            if (rand(1, 100) <= 70) {
                Prescription::create([
                    'patient_id' => $visit->patient_id,
                    'doctor_id'  => $visit->doctor_id,
                    'visit_id'   => $visit->id,
                    'notes'      => $notes[array_rand($notes)],
                ]);
            }
        }
    }
}
