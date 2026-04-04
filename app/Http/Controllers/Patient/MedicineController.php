<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;

class MedicineController extends Controller
{
    // البحث عن دواء ومعرفة توفره (بدون أرقام دقيقة)
    public function search(Request $request)
    {
        $query = $request->get('query');
        $centerId = $request->get('medical_center_id');
        
        $patient = auth()->user()->patient;
        
        // جلب معرفات المراكز التي زارها المريض أو صرف منها
        $visitedCenterIds = \App\Models\MedicalCenter::whereHas('visits', function($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->orWhereHas('dispenses.prescriptionItem.prescription', function($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })->pluck('id');

        $medicines = collect([]);
        
        if ($query) {
            $medicines = Medicine::searchArabic(['name', 'name_en'], $query)
                ->whereHas('inventories', function ($q) use ($visitedCenterIds, $centerId) {
                    if ($centerId && $centerId !== 'all') {
                        $q->where('medical_center_id', $centerId);
                    } else {
                        $q->whereIn('medical_center_id', $visitedCenterIds);
                    }
                })
                ->with(['inventories' => function($q) use ($visitedCenterIds, $centerId) {
                    if ($centerId && $centerId !== 'all') {
                        $q->where('medical_center_id', $centerId);
                    } else {
                        $q->whereIn('medical_center_id', $visitedCenterIds);
                    }
                    $q->with('medicalCenter');
                }])
                ->get();
        }

        $medicalCenters = \App\Models\MedicalCenter::whereIn('id', $visitedCenterIds)->get();

        return view('patient.medicines.search', compact('medicines', 'medicalCenters'));
    }
}
