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


        $todayPatientsCount = Visit::where('doctor_id', $doctor->id)
            ->whereDate('visit_date', $today)
            ->distinct('patient_id')
            ->count('patient_id');// بيجيب عدد المرضى اللي تم الكشف عليهم


            $todayPrescriptions = Prescription::where('doctor_id', $doctor->id)
            ->whereDate('created_at', $today)
            ->with('items.dispenses')
            ->get();// بيجيب الروشتات اللي تم كتابتها اليوم


            $dispensedCount = $todayPrescriptions->filter(function ($p) {
            return $p->items->count() > 0 && $p->items->every(fn($item) => $item->dispenses->count() > 0);
        })->count();// بيجيب عدد الروشتات اللي تم صرفها


        $undispensedCount = $todayPrescriptions->count() - $dispensedCount;// بيجيب عدد الروشتات اللي لم يتم صرفها


        $recentPrescriptions = Prescription::where('doctor_id', $doctor->id)
            ->with(['patient', 'items.dispenses'])
            ->latest()
            ->take(10)
            ->get();// بيجيب اخر 10 روشتات

        return view('doctor.dashboard', compact(
            'todayPatientsCount',
            'dispensedCount',
            'undispensedCount',
            'recentPrescriptions'
        ));
    }
}
