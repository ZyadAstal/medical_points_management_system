<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Dispense;
use App\Models\Inventory;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DispenseController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'exists:prescription_items,id',
        ]);

        $itemIds = $request->input('items');
        $pharmacist = Auth::user();
        $centerId = $pharmacist->medical_center_id;

        if (!$centerId) {
            return back()->with('error', 'الصيدلي غير مسجل في مركز طبي.');
        }

        try {
            DB::beginTransaction();
            
            $itemsToDispense = PrescriptionItem::whereIn('id', $itemIds)
                                ->where('is_dispensed', false)
                                ->with(['medicine', 'prescription.patient'])
                                ->get();

            if ($itemsToDispense->isEmpty()) {
                return back()->with('error', 'لم يتم اختيار عناصر صالحة للصرف.');
            }

            $totalPointsCost = 0;
            $patient = $itemsToDispense->first()->prescription->patient;

            // 1. Calculate Total Cost & Check Inventory
            foreach ($itemsToDispense as $item) {
                $inventory = Inventory::where('medical_center_id', $centerId)
                                      ->where('medicine_id', $item->medicine_id)
                                      ->lockForUpdate()
                                      ->first();

                if (!$inventory || $inventory->quantity < 1) {
                    throw new \Exception("الدواء {$item->medicine->name} غير متوفر في المخزون.");
                }

                $totalPointsCost += $item->medicine->points_cost;
            }

            // 2. Check Patient Balance
            if ($patient->points < $totalPointsCost) {
                return back()->with('error', "رصيد المريض غير كافٍ. المطلوب: $totalPointsCost، المتوفر: {$patient->points}");
            }

            // 3. Process Dispense
            foreach ($itemsToDispense as $item) {
                // Deduct Inventory
                $inventory = Inventory::where('medical_center_id', $centerId)
                                      ->where('medicine_id', $item->medicine_id)
                                      ->first();
                $inventory->decrement('quantity');

                // Mark Item as Dispensed
                $item->update(['is_dispensed' => true]);

                // Create Dispense Record
                Dispense::create([
                    'prescription_item_id' => $item->id,
                    'pharmacist_id' => $pharmacist->id,
                    'medical_center_id' => $centerId,
                    'points_used' => $item->medicine->points_cost,
                ]);
            }

            // 4. Deduct Points
            $patient->decrement('points', $totalPointsCost);

            // 5. Cleanup / Check (Optional: You could check if all items are dispensed here but don't update non-existent status)
            foreach ($itemsToDispense->pluck('prescription_id')->unique() as $pId) {
                // No status column to update
            }

            DB::commit();
            return back()->with('success', 'تم صرف الأدوية المختارة بنجاح وتم خصم النقاط.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الصرف: ' . $e->getMessage());
        }
    }
    public function history()
    {
        $dispenses = Dispense::where('pharmacist_id', Auth::id())
            ->with(['prescriptionItem.medicine', 'prescriptionItem.prescription.patient'])
            ->latest()
            ->paginate(15);

        return view('pharmacist.dispensing.index', compact('dispenses'));
    }

    public function manualStore(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'items' => 'required|array',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);

        $pharmacist = Auth::user();
        $centerId = $pharmacist->medical_center_id;

        if (!$centerId) {
            return back()->with('error', 'الصيدلي غير مسجل في مركز طبي.');
        }

        try {
            DB::beginTransaction();

            $patient = Patient::findOrFail($request->patient_id);
            $totalPointsCost = 0;
            $itemsData = $request->input('items', []);

            // 1. Create Prescription (Fixed non-existent fields)
            $prescription = Prescription::create([
                'patient_id' => $patient->id,
                'doctor_id' => $request->doctor_id, 
                'notes' => $request->notes,
            ]);

            // 2. Process Items
            foreach ($itemsData as $data) {
                $medicineId = $data['medicine_id'];
                $quantity = $data['quantity'];
                
                $medicine = \App\Models\Medicine::findOrFail($medicineId);
                $isDispensed = ($quantity > 0);
                $cost = 0;

                if ($isDispensed) {
                    // Check Inventory
                    $inventory = Inventory::where('medical_center_id', $centerId)
                                          ->where('medicine_id', $medicineId)
                                          ->lockForUpdate()
                                          ->first();

                    if (!$inventory || $inventory->quantity < $quantity) {
                        throw new \Exception("الدواء {$medicine->name} غير متوفر بالكمية المطلوبة (المتوفر: " . ($inventory->quantity ?? 0) . ")");
                    }

                    $cost = $medicine->points_cost * $quantity;
                    $totalPointsCost += $cost;

                    // Deduct Inventory
                    $inventory->decrement('quantity', $quantity);
                }

                // Create Prescription Item
                $pItem = PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'medicine_id' => $medicineId,
                    'quantity' => $quantity,
                    'is_dispensed' => $isDispensed,
                ]);

                if ($isDispensed) {
                    // Create Dispense Record
                    Dispense::create([
                        'prescription_item_id' => $pItem->id,
                        'pharmacist_id' => $pharmacist->id,
                        'medical_center_id' => $centerId,
                        'points_used' => $cost,
                        'quantity' => $quantity, // Track quantity in dispense
                    ]);
                }
            }

            // 3. Check & Deduct Points
            if ($patient->points < $totalPointsCost) {
                throw new \Exception("رصيد المريض غير كافٍ. المطلوب: $totalPointsCost، المتوفر: {$patient->points}");
            }

            if ($totalPointsCost > 0) {
                $patient->decrement('points', $totalPointsCost);
            }

            DB::commit();
            return redirect()->route('pharmacist.prescriptions.create')->with('success', 'تم تسجيل العملية بنجاح.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ: ' . $e->getMessage())->withInput();
        }
    }
}
