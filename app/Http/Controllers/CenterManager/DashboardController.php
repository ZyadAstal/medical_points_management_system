<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Dispense;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $center_id = $user->medical_center_id;

        if (!$center_id) {
            abort(403, 'User is not assigned to any medical center.');
        }

        $low_stock_count = Inventory::where('medical_center_id', $center_id)
            ->where('quantity', '<', 10)
            ->count();

        $total_medicines = Inventory::where('medical_center_id', $center_id)->count();

        $dispensed_today = Dispense::where('medical_center_id', $center_id)
            ->whereDate('created_at', now())
            ->count();

        $recent_dispenses = Dispense::where('medical_center_id', $center_id)
            ->with(['prescriptionItem.medicine', 'prescriptionItem.prescription.patient'])
            ->latest()
            ->take(5)
            ->get();

        $staff = $user->medicalCenter->users()->with('role')->get();

        return view('manager.dashboard', compact(
            'low_stock_count', 
            'total_medicines', 
            'dispensed_today', 
            'recent_dispenses', 
            'staff'
        ));
    }
}
