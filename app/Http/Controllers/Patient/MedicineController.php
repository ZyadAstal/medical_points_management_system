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
        
        $medicines = collect([]);
        
        if ($query) {
            $medicines = Medicine::searchArabic('name', $query)
                ->with(['inventories.medicalCenter'])
                ->when($centerId && $centerId !== 'all', function ($q) use ($centerId) {
                    $q->whereHas('inventories', function ($innerQ) use ($centerId) {
                        $innerQ->where('medical_center_id', $centerId);
                    });
                })
                ->get();
        }

        $medicalCenters = \App\Models\MedicalCenter::all();

        return view('patient.medicines.search', compact('medicines', 'medicalCenters'));
    }
}
