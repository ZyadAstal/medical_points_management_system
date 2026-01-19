<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function search(Request $request)
    {
        $prescriptions = collect();
        $patient = null;

        if ($request->has('national_id')) {
            $nid = $request->input('national_id');
            
            // البحث عن المريض
            $patient = \App\Models\Patient::where('national_id', $nid)->first();

            if ($patient) {
                // جلب وصفات هذا المريض
                $prescriptions = Prescription::where('patient_id', $patient->id)
                    ->with(['doctor', 'items.medicine'])
                    ->latest()
                    ->get();
            }
        }

        return view('pharmacist.prescriptions.search', compact('prescriptions', 'patient'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items.medicine', 'items.dispense']);
        return view('pharmacist.prescriptions.show', compact('prescription'));
    }

    public function create()
    {
        $doctors = User::whereHas('role', function($query) {
            $query->where('name', 'Doctor');
        })->get();
        
        $medicines = \App\Models\Medicine::all();

        return view('pharmacist.prescriptions.create', compact('doctors', 'medicines'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_national_id' => 'required|exists:patients,national_id',
            'doctor_id' => 'required|exists:users,id',
            'medicines' => 'required|array',
            'medicines.*.id' => 'required|exists:medicines,id',
            'medicines.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $patient = \App\Models\Patient::where('national_id', $request->patient_national_id)->first();

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id,
                'notes' => $request->notes,
                'status' => 'new',
            ]);

            foreach ($request->medicines as $item) {
                \App\Models\PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'is_dispensed' => false,
                ]);
            }

            DB::commit();
            return redirect()->route('pharmacist.prescriptions.search', ['national_id' => $patient->national_id])
                             ->with('success', 'تم إنشاء الوصفة الطبية بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حفظ الوصفة: ' . $e->getMessage())->withInput();
        }
    }
}
