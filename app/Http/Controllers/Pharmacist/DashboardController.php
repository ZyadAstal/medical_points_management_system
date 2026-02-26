<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\Dispense;

class DashboardController extends Controller
{
    public function index()
    {
        $center_id = auth()->user()->medical_center_id;
        $today = \Carbon\Carbon::today();

        // 1. Total Dispensed Today (Records in this center)
        $dispensed_count = Dispense::where('medical_center_id', $center_id)
            ->whereDate('created_at', $today)
            ->count();

        // 2. Points Dispensed Today (Sum in this center)
        $points_dispensed = Dispense::where('medical_center_id', $center_id)
            ->whereDate('created_at', $today)
            ->sum('points_used');

        // 3. Patients Today (All visits in this center today)
        $patients_served_count = \App\Models\Visit::where('medical_center_id', $center_id)
            ->whereDate('visit_date', $today)
            ->distinct('patient_id')
            ->count('patient_id');

        // 4. Waiting for Doctor (Visits with status 'waiting' in this center today)
        $waiting_for_doctor_count = \App\Models\Visit::where('medical_center_id', $center_id)
            ->where('status', \App\Models\Visit::STATUS_WAITING)
            ->whereDate('visit_date', $today)
            ->count();

        // 5. Recent 5 dispenses in this center
        $recent_dispenses = Dispense::where('medical_center_id', $center_id)
            ->with(['prescriptionItem.medicine', 'prescriptionItem.prescription.patient'])
            ->latest()
            ->take(5)
            ->get();

        // 6. Stock alerts (Medicines with low quantity in this center)
        $stock_alerts = \App\Models\Inventory::where('medical_center_id', $center_id)
            ->where('quantity', '<', 10)
            ->with('medicine')
            ->get();

        return view('pharmacist.dashboard', compact(
            'dispensed_count', 
            'points_dispensed', 
            'patients_served_count', 
            'waiting_for_doctor_count',
            'recent_dispenses',
            'stock_alerts'
        ));
    }
}
