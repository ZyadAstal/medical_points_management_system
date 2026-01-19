<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index()
    {
        // عرض صفحة البحث
        return view('doctor.patients.search');
    }

    public function search(Request $request)
    {
        $request->validate([
            'national_id' => 'required|string',
        ]);

        $search = $request->input('national_id');
        $patient = Patient::where('national_id', $search)->first();

        return view('doctor.patients.search', compact('patient', 'search'));
    }

    public function show(Patient $patient)
    {
        $patient->load(['prescriptions.items', 'prescriptions.doctor']); // Load history
        return view('doctor.patients.show', compact('patient'));
    }
}
