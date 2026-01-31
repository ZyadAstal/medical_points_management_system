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

        $query = Dispense::where('dispenses.medical_center_id', $centerId)
            ->join('prescription_items', 'dispenses.prescription_item_id', '=', 'prescription_items.id')
            ->join('prescriptions', 'prescription_items.prescription_id', '=', 'prescriptions.id')
            ->select('dispenses.*')
            ->with(['prescriptionItem.medicine', 'pharmacist', 'prescriptionItem.prescription.patient']);

        // Date Filter
        if ($request->filled('date')) {
            $query->whereDate('dispenses.created_at', $request->date);
        }

        // Pharmacist Filter
        if ($request->filled('pharmacist_id')) {
            $query->where('dispenses.pharmacist_id', $request->pharmacist_id);
        }

        // Patient Filter
        if ($request->filled('patient_id')) {
            $query->where('prescriptions.patient_id', $request->patient_id);
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

        $dispenses = $query->latest('dispenses.created_at')->paginate(10);
        
        // Get Pharmacists for filter dropdown
        $pharmacists = User::where('medical_center_id', $centerId)
            ->whereHas('role', function($q) {
                $q->where('name', 'Pharmacist');
            })->get();

        // Get Patients for filter dropdown
        $patients = Patient::whereHas('user', function($q) use ($centerId) {
            $q->where('medical_center_id', $centerId);
        })->orWhereHas('visits', function($q) use ($centerId) {
            $q->where('medical_center_id', $centerId);
        })->get();

        return view('manager.morphology', compact('dispenses', 'pharmacists', 'patients'));
    }
}
