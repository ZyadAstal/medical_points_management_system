<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispense;
use App\Models\Medicine;
use App\Models\MedicalCenter;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. Total points used overall
        $totalPointsUsed = Dispense::sum('points_used');

        // 2. Activity per Center
        $centersActivity = MedicalCenter::withCount('visits', 'inventories')
            ->get()
            ->map(function($center) {
                $center->total_dispenses = Dispense::where('medical_center_id', $center->id)->count();
                $center->points_used = Dispense::where('medical_center_id', $center->id)->sum('points_used');
                return $center;
            });

        // 3. Most dispensed medicines
        $topMedicines = DB::table('dispenses')
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('medicines', 'prescription_items.medicine_id', '=', 'medicines.id')
            ->select('medicines.name', DB::raw('count(dispenses.id) as count'), DB::raw('sum(dispenses.points_used) as total_points'))
            ->groupBy('medicines.id', 'medicines.name')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        return view('superadmin.reports.index', compact('totalPointsUsed', 'centersActivity', 'topMedicines'));
    }
}
