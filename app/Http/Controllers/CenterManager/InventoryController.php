<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Inventory;
use App\Models\Medicine;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $center_id = $user->medical_center_id;

        if (!$center_id) {
            abort(403, 'User is not assigned to any medical center.');
        }

        $query = Inventory::where('medical_center_id', $center_id)->with('medicine');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->whereHas('medicine', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $inventories = $query->get();
        $all_medicines = Medicine::all();

        return view('manager.inventory.index', compact('inventories', 'all_medicines'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $user = Auth::user();
        $center_id = $user->medical_center_id;

        Inventory::updateOrCreate(
            [
                'medical_center_id' => $center_id,
                'medicine_id' => $request->medicine_id
            ],
            [
                'quantity' => $request->quantity
            ]
        );

        return redirect()->back()->with('success', 'تم تحديث المخزون بنجاح');
    }
}
