<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MedicalCenter;
use Illuminate\Http\Request;

class MedicalCenterController extends Controller
{
    public function index()
    {
        $centers = MedicalCenter::all();
        return view('superadmin.centers.index', compact('centers'));
    }

    public function create()
    {
        return view('superadmin.centers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        MedicalCenter::create($request->all());

        return redirect()->route('superadmin.centers.index')->with('success', 'تم إضافة المركز بنجاح');
    }

    public function edit(MedicalCenter $center)
    {
        return view('superadmin.centers.edit', compact('center'));
    }

    public function update(Request $request, MedicalCenter $center)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $center->update($request->all());

        return redirect()->route('superadmin.centers.index')->with('success', 'تم تحديث بيانات المركز');
    }

    public function destroy(MedicalCenter $center)
    {
        $center->delete();
        return redirect()->route('superadmin.centers.index')->with('success', 'تم حذف المركز');
    }
}
