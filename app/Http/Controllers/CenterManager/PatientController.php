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

        // Hybrid Filter: Patients who belong to this center OR had interactions (visits/dispenses)
        $query = Patient::where(function($q) use ($centerId) {
            // 1. Registered at this center
            $q->whereHas('user', function($u) use ($centerId) {
                $u->where('medical_center_id', $centerId);
            })
            // 2. OR Visited this center
            ->orWhereHas('visits', function($v) use ($centerId) {
                $v->where('medical_center_id', $centerId);
            })
            // 3. OR Dispensed from this center
            ->orWhereHas('prescriptions.items.dispenses', function($ds) use ($centerId) {
                $ds->where('medical_center_id', $centerId);
            });
        });

        // Search Filter (Full Name or National ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        // Include visited centers labels for the view modal
        $patients = $query->with([
            'user.medicalCenter',
            'visits.medicalCenter',
            'prescriptions.dispenses' => function($q) use ($centerId) {
                $q->where('medical_center_id', $centerId)->latest();
            }
        ])->latest()->paginate(10);

        // Map centers for easy display
        $patients->getCollection()->each(function($patient) {
            $visitCenters = $patient->visits->map(fn($v) => optional($v->medicalCenter)->name)->filter()->unique()->values()->all();
            $regCenter = optional($patient->user->medicalCenter)->name;
            $patient->visited_centers_list = array_unique(array_merge([$regCenter], $visitCenters));
        });

        return view('manager.patients', compact('patients'));
    }

    public function show(Patient $patient)
    {
        $centerId = Auth::user()->medical_center_id;
        
        // Ensure patient is registered in this center OR has interacted with it
        $isRegistered = $patient->user->medical_center_id == $centerId;
        $hasInteracted = Visit::where('patient_id', $patient->id)->where('medical_center_id', $centerId)->exists() || 
                        Dispense::where('medical_center_id', $centerId)
                            ->whereHas('prescriptionItem.prescription', function($q) use ($patient) {
                                $q->where('patient_id', $patient->id);
                            })->exists();

        if (!$isRegistered && !$hasInteracted) {
            abort(403, 'Unauthorized access to patient data.');
        }

        // Add visited centers for display
        $visitCenters = $patient->visits->map(fn($v) => optional($v->medicalCenter)->name)->filter()->unique()->values()->all();
        $regCenter = optional($patient->user->medicalCenter)->name;
        $patient->visited_centers_list = array_unique(array_merge([$regCenter], $visitCenters));

        return view('manager.patients.show', compact('patient'));
    }
}
