<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $center_id = Auth::user()->medical_center_id;

        if (!$center_id) {
            return redirect()->route('pharmacist.dashboard')->with('error', 'انت غير مسجل في مركز طبي لرؤية المخزون.');
        }

        $search = $request->get('search');

        $query = Inventory::where('medical_center_id', $center_id)
            ->with('medicine');

        if ($search) {
            $query->whereHas('medicine', function($q) use ($search) {
                $q->searchArabic(['name', 'name_en'], $search);
            });
        }

        $inventory = $query->paginate(15)->appends(['search' => $search]);

        return view('pharmacist.inventory.index', compact('inventory'));
    }
}
