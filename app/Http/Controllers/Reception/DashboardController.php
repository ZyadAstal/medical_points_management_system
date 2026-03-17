<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visit;
use App\Models\Inventory;
use App\Models\Dispense;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today    = Carbon::today();
        $centerId = Auth::user()->medical_center_id;

        // زيارات اليوم في هذا المركز
        $todayVisitsCount = Visit::where('medical_center_id', $centerId)
            ->whereDate('visit_date', $today)
            ->count();

        // عمليات الصرف اليوم
        $dispensesCountToday = Dispense::where('medical_center_id', $centerId)
            ->whereDate('created_at', $today)
            ->count();

        // النقاط المصروفة اليوم
        $pointsDispensedToday = Dispense::where('medical_center_id', $centerId)
            ->whereDate('created_at', $today)
            ->sum('points_used') ?? 0;

        // الوصفات الجزئية (بها عناصر لم تُصرف)
        // نأخذ المرضى في هذا المركز من خلال زياراتهم اليوم
        $patientIdsToday = Visit::where('medical_center_id', $centerId)
            ->whereDate('visit_date', $today)
            ->pluck('patient_id');

        $partialPrescriptions = Prescription::whereIn('patient_id', $patientIdsToday)
            ->whereHas('items', fn($q) => $q->where('is_dispensed', false))
            ->count();

        // الأدوية القاربة على النفاذ (< 10)
        $lowStockCount = Inventory::where('medical_center_id', $centerId)
            ->where('quantity', '<', 10)
            ->count();

        // أول 3 مرضى في الدور اليوم (بنفس ترتيب قائمة الانتظار)
        $recentVisits = Visit::with(['patient', 'doctor'])
            ->where('medical_center_id', $centerId)
            ->whereDate('visit_date', $today)
            ->orderByRaw("CASE 
                WHEN status = '" . Visit::STATUS_REGISTERED . "' THEN 1
                WHEN status = '" . Visit::STATUS_WAITING . "' THEN 2
                WHEN status = '" . Visit::STATUS_IN_PROGRESS . "' THEN 3
                WHEN status = '" . Visit::STATUS_COMPLETED . "' THEN 4
                WHEN status = '" . Visit::STATUS_CANCELLED . "' THEN 5
                ELSE 6 END")
            ->orderBy('created_at', 'asc')
            ->take(3)
            ->get();

        return view('reception.dashboard', compact(
            'todayVisitsCount',
            'dispensesCountToday',
            'pointsDispensedToday',
            'partialPrescriptions',
            'lowStockCount',
            'recentVisits'
        ));
    }
}
