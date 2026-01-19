<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::all();
        return view('superadmin.medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('superadmin.medicines.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points_cost' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        Medicine::create($request->all());

        return redirect()->route('superadmin.medicines.index')->with('success', 'تم تعريف الدواء بنجاح');
    }

    public function edit(Medicine $medicine)
    {
        return view('superadmin.medicines.edit', compact('medicine'));
    }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'points_cost' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        $medicine->update($request->all());

        return redirect()->route('superadmin.medicines.index')->with('success', 'تم تحديث الدواء');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('superadmin.medicines.index')->with('success', 'تم حذف الدواء');
    }
}
