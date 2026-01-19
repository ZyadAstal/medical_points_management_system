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
    public function index()
    {
        $centerId = Auth::user()->medical_center_id;

        // 1. Total points used in this center
        $totalPointsUsed = Dispense::where('medical_center_id', $centerId)->sum('points_used');

        // 2. Summary of medicines dispensed in this center
        $medicineStats = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->where('dispenses.medical_center_id', $centerId)
            ->select('medicines.name', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc')
            ->get();

        // 3. Inventory health (low stock items)
        $lowStock = Inventory::where('medical_center_id', $centerId)
            ->where('quantity', '<', 10)
            ->with('medicine')
            ->get();

        return view('manager.reports.index', compact('totalPointsUsed', 'medicineStats', 'lowStock'));
    }
}
