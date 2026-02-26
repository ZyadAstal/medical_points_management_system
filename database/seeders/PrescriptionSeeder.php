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
        $doctors = User::whereHas('role', function($q) { $q->where('name', 'Doctor'); })->get();
        $patients = Patient::all();
        $notes = ['زيارة دورية', 'متابعة حالة', 'مراجعة نتائج تحاليل', 'فحص عام'];

        foreach ($patients as $patient) {
            if (rand(0, 1)) { // Create a prescription for half of the patients
                Prescription::create([
                    'patient_id' => $patient->id,
                    'doctor_id'  => $doctors->random()->id,
                    'notes'      => $notes[array_rand($notes)],
                ]);
            }
        }
    }
}
