<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();

        // 1. Count of prescriptions by this doctor
        $prescriptionsCount = Prescription::where('doctor_id', $doctor->id)->count();

        // 2. Distinct patients count
        $patientsCount = Prescription::where('doctor_id', $doctor->id)
            ->distinct('patient_id')
            ->count('patient_id');

        // 3. Recent 5 prescriptions
        $recentPrescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with('patient')
            ->latest()
            ->take(5)
            ->get();

        // 4. Waiting List (Today)
        $waitingVisits = \App\Models\Visit::where('doctor_id', $doctor->id)
            ->where('status', 'waiting')
            ->whereDate('visit_date', now())
            ->with('patient')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('doctor.dashboard', compact('prescriptionsCount', 'patientsCount', 'recentPrescriptions', 'waitingVisits'));
    }
}
