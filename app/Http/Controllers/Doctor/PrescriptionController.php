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

        // Get patients who have prescriptions from this doctor, paginated
        $patients = Patient::whereHas('prescriptions', function($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })
        ->orderBy('full_name')
        ->paginate(20)
        ->withQueryString();

        return view('doctor.recipes-record', compact('patients'));
    }

    /**
     * Show prescriptions for a specific patient
     */
    public function show(Patient $patient)
    {
        $doctor = Auth::user();

        // Paginate patients list for the sidebar
        $patients = Patient::whereHas('prescriptions', function($q) use ($doctor) {
            $q->where('doctor_id', $doctor->id);
        })
        ->orderBy('full_name')
        ->paginate(20)
        ->withQueryString();

        // Paginate prescriptions for the selected patient
        $prescriptions = Prescription::where('doctor_id', $doctor->id)
            ->where('patient_id', $patient->id)
            ->with(['pharmacist', 'items.medicine', 'items.dispenses.pharmacist', 'items.dispenses.medicalCenter'])
            ->latest()
            ->paginate(5, ['*'], 'pres_page')
            ->withQueryString();

        return view('doctor.recipes-record', compact('patients', 'prescriptions', 'patient'))
            ->with('selectedPatient', $patient);
    }
}
