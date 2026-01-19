<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function create(Patient $patient)
    {
        $centerId = Auth::user()->medical_center_id;
        
        // جلب الأطباء في نفس المركز
        $doctors = User::where('medical_center_id', $centerId)
            ->whereHas('role', function($query) {
                $query->where('name', 'Doctor');
            })->get();

        return view('reception.visits.create', compact('patient', 'doctors'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'priority' => 'required|integer|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        $centerId = Auth::user()->medical_center_id;

        Visit::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'medical_center_id' => $centerId,
            'visit_date' => now()->toDateString(),
            'priority' => $request->priority,
            'status' => 'waiting',
            'notes' => $request->notes,
        ]);

        return redirect()->route('reception.patients.index')->with('success', 'تم إضافة المريض لقائمة الانتظار بنجاح');
    }
}
