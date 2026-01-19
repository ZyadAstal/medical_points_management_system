<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        
        // إحصائيات للمريض
        $points_balance = $patient ? $patient->points : 0;
        $prescriptions_count = $patient ? $patient->prescriptions()->count() : 0;
        
        // الأدوية المصروفة (من خلال الوصفات وعناصرها التي تم صرفها)
        $dispensed_medicines_count = $patient ? $patient->prescriptions()
            ->join('prescription_items', 'prescriptions.id', '=', 'prescription_items.prescription_id')
            ->join('dispenses', 'prescription_items.id', '=', 'dispenses.prescription_item_id')
            ->count() : 0;

        return view('patient.dashboard', compact('points_balance', 'prescriptions_count', 'dispensed_medicines_count'));
    }
}
