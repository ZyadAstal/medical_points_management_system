<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PrescriptionItem;
use App\Models\Dispense;
use App\Models\Inventory;
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

            DB::commit();
            return back()->with('success', 'تم صرف الأدوية المختارة بنجاح وتم خصم النقاط.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الصرف: ' . $e->getMessage());
        }
    }
}
