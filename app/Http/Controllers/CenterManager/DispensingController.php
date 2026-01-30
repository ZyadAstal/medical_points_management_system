<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use App\Models\Dispense;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispensingController extends Controller
{
    public function index(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;

        $query = Dispense::where('medical_center_id', $centerId)
            ->with(['prescriptionItem.prescription.patient', 'prescriptionItem.medicine', 'pharmacist']);

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Pharmacist Filter
        if ($request->filled('pharmacist_id')) {
            $query->where('pharmacist_id', $request->pharmacist_id);
        }

        // Search Filter (Patient Name or Medicine Name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('prescriptionItem.prescription.patient', function($pq) use ($search) {
                    $pq->where('full_name', 'like', "%{$search}%");
                })->orWhereHas('prescriptionItem.medicine', function($mq) use ($search) {
                    $mq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $dispenses = $query->latest()->paginate(10);
        
        // Get Pharmacists for filter dropdown
        $pharmacists = User::where('medical_center_id', $centerId)
            ->whereHas('role', function($q) {
                $q->where('name', 'Pharmacist');
            })->get();

        return view('manager.morphology', compact('dispenses', 'pharmacists'));
    }
}
