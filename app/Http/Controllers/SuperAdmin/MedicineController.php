<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicine::query(); 

        if ($request->filled('search')) {
            // البحث في الاسم العربي والإنجليزي معاً
            $query->searchArabic(['name', 'name_en'], $request->search);
        }

        $medicines = $query->paginate(10)->withQueryString();
        return view('superadmin.medicines', compact('medicines'));
    }

    // public function create()
    // {
    //     return view('superadmin.medicines.create');
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'points_cost' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        Medicine::create($request->only(['name', 'name_en', 'points_cost', 'expiry_date']));

        return redirect()->route('superadmin.medicines.index')->with('success', 'تم تعريف الدواء بنجاح');
    }

    // public function edit(Medicine $medicine)
    // {
    //     return view('superadmin.medicines.edit', compact('medicine'));
    // }

    public function update(Request $request, Medicine $medicine)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'name_en'     => 'nullable|string|max:255',
            'points_cost' => 'required|integer',
            'expiry_date' => 'required|date',
        ]);

        $medicine->update($request->only(['name', 'name_en', 'points_cost', 'expiry_date']));

        return redirect()->route('superadmin.medicines.index')->with('success', 'تم تحديث الدواء');
    }

    public function destroy(Medicine $medicine)
    {
        $medicine->delete();
        return redirect()->route('superadmin.medicines.index')->with('success', 'تم حذف الدواء');
    }
}
