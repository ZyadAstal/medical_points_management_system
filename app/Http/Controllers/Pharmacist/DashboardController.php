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
        $today = \Carbon\Carbon::today();

        // 1. Dispensed count today
        $dispensed_count = Dispense::where('pharmacist_id', auth()->id())
            ->whereDate('created_at', $today)
            ->count();

        // 2. Points dispensed today
        $points_dispensed = Dispense::where('pharmacist_id', auth()->id())
            ->whereDate('created_at', $today)
            ->sum('points_used');

        // 3. Patients served today (Unique patients)
        $patients_served_count = Dispense::where('dispenses.pharmacist_id', auth()->id())
            ->whereDate('dispenses.created_at', $today)
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('prescriptions', 'prescription_items.prescription_id', '=', 'prescriptions.id')
            ->distinct('prescriptions.patient_id')
            ->count('prescriptions.patient_id');

        // 4. Recent 5 dispenses
        $recent_dispenses = Dispense::where('pharmacist_id', auth()->id())
            ->with(['prescriptionItem.medicine', 'prescriptionItem.prescription.patient'])
            ->latest()
            ->take(5)
            ->get();

        return view('pharmacist.dashboard', compact('dispensed_count', 'points_dispensed', 'patients_served_count', 'recent_dispenses'));
    }
}
