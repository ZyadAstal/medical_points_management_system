<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Patient;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    /**
     * List all patients who have prescriptions from this doctor
     */
    public function index()
    {
        $doctor = Auth::user();

        $patientIds = Prescription::where('doctor_id', $doctor->id)
            ->distinct()
            ->pluck('patient_id');

        $patients = Patient::whereIn('id', $patientIds)
            ->orderBy('full_name')
            ->get();

        return view('doctor.recipes-record', compact('patients'));
    }

    /**
     * Show prescriptions for a specific patient
     */
    public function show(Patient $patient)
    {
        $doctor = Auth::user();

        $patientIds = Prescription::where('doctor_id', $doctor->id)
            ->distinct()
            ->pluck('patient_id');

        $patients = Patient::whereIn('id', $patientIds)
            ->orderBy('full_name')
            ->get();

        $prescriptions = Prescription::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->with(['items.medicine', 'items.dispenses.pharmacist', 'items.dispenses.medicalCenter'])
            ->latest()
            ->get();

        return view('doctor.recipes-record', compact('patients', 'prescriptions', 'patient'))
            ->with('selectedPatient', $patient);
    }
}
