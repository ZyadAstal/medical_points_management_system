<?php

namespace App\Http\Controllers\Pharmacist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index()
    {
        $center_id = Auth::user()->medical_center_id;

        if (!$center_id) {
            return redirect()->route('pharmacist.dashboard')->with('error', 'انت غير مسجل في مركز طبي لرؤية المخزون.');
        }

        $inventory = Inventory::where('medical_center_id', $center_id)
            ->with('medicine')
            ->paginate(15);

        return view('pharmacist.inventory.index', compact('inventory'));
    }
}
