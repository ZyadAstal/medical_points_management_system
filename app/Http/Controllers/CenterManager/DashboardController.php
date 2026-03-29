<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Dispense;
use App\Models\Visit;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $center_id = $user->medical_center_id; // هات المركز المرتبط بالمستخدم

        if (!$center_id) {
            abort(403, 'User is not assigned to any medical center.'); // لو المستخدم مش مرتبط باي مركز الغي الطلب
        }

        $low_stock_count = Inventory::where('medical_center_id', $center_id)
            ->where('quantity', '<', 10)
            ->count(); // هات عدد الادوية الي كميتها اقل من 10

        $total_medicines = Inventory::where('medical_center_id', $center_id)->count(); // هات عدد الادوية

        $dispensed_today = Dispense::where('medical_center_id', $center_id)
            ->whereDate('created_at', now())
            ->count(); // هات عدد الادوية الي تم صرفها اليوم

        $recent_dispenses = Dispense::where('medical_center_id', $center_id)
            ->with(['prescriptionItem.medicine', 'prescriptionItem.prescription.patient'])
            ->latest()
            ->take(5)
            ->get(); // هات اخر 5 صرفيات

        $patients_count = Visit::where('medical_center_id', $center_id)
            ->distinct('patient_id')
            ->count('patient_id'); // هات عدد المرضى

        $doctors_count = $user->medicalCenter->users()->whereHas('role', function ($q) {
            $q->where('name', 'Doctor'); // هات عدد الدكاترة
        })->count();

        $pharmacists_count = $user->medicalCenter->users()->whereHas('role', function ($q) {
            $q->where('name', 'Pharmacist'); // هات عدد الصيادلة
        })->count();

        $points_today = Dispense::where('medical_center_id', $center_id)
            ->whereDate('created_at', now())
            ->sum('points_used'); // هات عدد النقاط المستخدمة اليوم

        return view('manager.dashboard', compact(
            'low_stock_count',
            'total_medicines',
            'dispensed_today',
            'recent_dispenses',
            'patients_count',
            'doctors_count',
            'pharmacists_count',
            'points_today'
        ));
    }
}
