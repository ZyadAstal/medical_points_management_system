<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $center_id = auth()->user()->medical_center_id;
        $query = Prescription::whereHas('doctor', function($q) use ($center_id) {
            $q->where('medical_center_id', $center_id);
        })->with(['patient', 'doctor', 'items.medicine']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            $filter = $request->filter;
            if ($filter == 'today') {
                $query->whereDate('created_at', now()->toDateString());
            } elseif ($filter == 'week') {
                $query->where('created_at', '>=', now()->subDays(7));
            }
            // 'patients' or 'all' defaults to no date filter (already handled by base query)
        } else {
            // Default filter to today if no filter is provided
            $query->whereDate('created_at', now()->toDateString());
        }

        $prescriptions = $query->latest()->paginate(15);
        return view('pharmacist.prescriptions.index', compact('prescriptions'));
    }

    public function search(Request $request)
    {
        // AJAX search for patient (used in wizard)
        if ($request->ajax()) {
            $nid = $request->input('national_id');
            $patient = \App\Models\Patient::where('national_id', $nid)->first();
            
            if ($patient) {
                return response()->json([
                    'success' => true,
                    'patient' => $patient,
                    'prescriptions' => Prescription::where('patient_id', $patient->id)
                        ->whereHas('items', function($q) {
                            $q->where('is_dispensed', false);
                        })
                        ->with(['doctor', 'items.medicine'])
                        ->latest()
                        ->get()
                ]);
            }
            return response()->json(['success' => false, 'message' => 'المريض غير موجود']);
        }

        // Fallback for non-ajax
        return redirect()->route('pharmacist.prescriptions.index');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['patient', 'doctor', 'items.medicine', 'items.dispense']);
        return view('pharmacist.prescriptions.show', compact('prescription'));
    }

    public function create()
    {
        $centerId = auth()->user()->medical_center_id;
        $doctors = User::where('medical_center_id', $centerId)
            ->whereHas('role', function($query) {
                $query->where('name', 'Doctor');
            })->get();
        
        $medicines = \App\Models\Medicine::whereHas('inventories', function($query) use ($centerId) {
            $query->where('medical_center_id', $centerId);
        })->with(['inventories' => function($query) use ($centerId) {
            $query->where('medical_center_id', $centerId);
        }])->get();

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
