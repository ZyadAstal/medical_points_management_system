<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dispense;

class DispenseController extends Controller
{
    // عرض ما تم صرفه للمريض فقط
    public function index()
    {
        $patient = Auth::user()->patient;
        
        if (!$patient) {
            return view('patient.dispenses.index', ['dispenses' => collect([])]);
        }

        // جلب الصرفيات المرتبطة بعناصر الوصفات الخاصة بالمريض
        $dispenses = Dispense::whereHas('prescriptionItem.prescription', function ($q) use ($patient) {
            $q->where('patient_id', $patient->id);
        })
        ->with(['prescriptionItem.medicine', 'medicalCenter'])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('patient.dispenses.index', compact('dispenses'));
    }
}
