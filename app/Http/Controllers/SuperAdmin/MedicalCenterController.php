<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MedicalCenter;
use Illuminate\Http\Request;

class MedicalCenterController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalCenter::withCount(['users', 'dispenses']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $centers = $query->paginate(10)->withQueryString();
        return view('superadmin.medical-centers', compact('centers'));
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
