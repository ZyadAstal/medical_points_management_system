<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Prescription;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user();
        $today = now()->toDateString();

        // 1. Today's patients count (from visits)
        $todayPatientsCount = Visit::where('doctor_id', $doctor->id)
            ->whereDate('visit_date', $today)
            ->distinct('patient_id')
            ->count('patient_id');

        // 2. Today's prescriptions by this doctor
        $todayPrescriptions = Prescription::where('doctor_id', $doctor->id)
            ->whereDate('created_at', $today)
            ->with('items.dispenses')
            ->get();

        // 3. Dispensed count (all items dispensed)
        $dispensedCount = $todayPrescriptions->filter(function ($p) {
            return $p->items->count() > 0 && $p->items->every(fn($item) => $item->dispenses->count() > 0);
        })->count();

        // 4. Undispensed count
        $undispensedCount = $todayPrescriptions->count() - $dispensedCount;

        // 5. Recent prescriptions (last 10) for updates section
        $recentPrescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with(['patient', 'items.dispenses'])
            ->latest()
            ->take(10)
            ->get();

        return view('doctor.dashboard', compact(
            'todayPatientsCount',
            'dispensedCount',
            'undispensedCount',
            'recentPrescriptions'
        ));
    }
}
