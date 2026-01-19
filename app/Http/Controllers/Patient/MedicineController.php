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
        $medicines = collect([]);
        
        if ($request->has('query')) {
            $query = $request->input('query');
            $medicines = Medicine::where('name', 'like', "%{$query}%")
                ->with(['inventories.medicalCenter']) // لجلب المراكز التي يتوفى فيها الدواء
                ->get();
        }

        return view('patient.medicines.search', compact('medicines'));
    }
}
