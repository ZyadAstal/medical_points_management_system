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
        $doctor = User::where('role_id', 4)->first();
        $patients = Patient::all();

        foreach ($patients as $patient) {
            Prescription::create([
                'patient_id' => $patient->id,
                'doctor_id'  => $doctor->id,
                'notes'      => 'الوصفة التجريبية',
            ]);
        }
    }
}
