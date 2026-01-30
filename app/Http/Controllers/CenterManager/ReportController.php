<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispense;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;
        $medicineId = $request->medicine_id;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;
        $reportType = $request->report_type;

        // Auto-set dates for 'Daily' report if not manually picked
        if ($reportType === 'daily' && !$fromDate && !$toDate) {
            $fromDate = now()->toDateString();
            $toDate = now()->toDateString();
        }

        // Base Query for Stats
        $statsQuery = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->where('dispenses.medical_center_id', $centerId)
            ->select('medicines.name', 'medicines.id', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc');

        // Apply filters
        if ($medicineId) $statsQuery->where('medicines.id', $medicineId);
        if ($fromDate) $statsQuery->where('dispenses.created_at', '>=', $fromDate);
        if ($toDate) $statsQuery->where('dispenses.created_at', '<=', $toDate . ' 23:59:59');

        $medicineStats = $statsQuery->get();

        // 1. Total points used (filtered)
        $totalPointsQuery = Dispense::where('medical_center_id', $centerId);
        if ($medicineId) $totalPointsQuery->whereHas('prescriptionItem', fn($q) => $q->where('medicine_id', $medicineId));
        if ($fromDate) $totalPointsQuery->where('created_at', '>=', $fromDate);
        if ($toDate) $totalPointsQuery->where('created_at', '<=', $toDate . ' 23:59:59');
        $totalPointsUsed = $totalPointsQuery->sum('points_used');

        // 3. Inventory health (unaffected by filters usually, but showing current state)
        $lowStock = Inventory::where('medical_center_id', $centerId)
            ->where('quantity', '<', 10)
            ->with('medicine')
            ->get();

        return view('manager.reports', compact('totalPointsUsed', 'medicineStats', 'lowStock'));
    }
}
