<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;

        // Optimized query to get patients who visited this center
        // We also want to include points and last dispense date
        $query = Patient::whereHas('visits', function($q) use ($centerId) {
            $q->where('medical_center_id', $centerId);
        });

        // Search Filter (Full Name or National ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        $patients = $query->with(['prescriptions.dispenses' => function($q) use ($centerId) {
            $q->where('medical_center_id', $centerId)->latest();
        }])->latest()->paginate(10);

        return view('manager.patients', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $centerId = Auth::user()->medical_center_id;
        
        // Ensure patient has visited this center
        $hasVisited = Visit::where('patient_id', $patient->id)
            ->where('medical_center_id', $centerId)
            ->exists();

        if (!$hasVisited) {
            abort(403, 'Unauthorized access to patient data.');
        }

        return view('manager.patients.show', compact('patient'));
    }
}
