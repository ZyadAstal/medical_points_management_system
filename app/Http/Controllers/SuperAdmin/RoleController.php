<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $allRoles = Role::all(); // Fetch all roles for the filter dropdown
        
        $query = Role::withCount('users'); // هات كل الادوار مع عدد المستخدمين المرتبطين بكل دور

        if ($request->filled('role')) {
            $query->where('name', $request->role); // لما المستخدم يختار قيمة دور هات كل السجلات الي اسمها بيساوي هادي القيمة 
        }

        $roles = $query->paginate(10)->withQueryString();
        return view('superadmin.roles', compact('roles', 'allRoles'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);
        
        $role->update(['name' => $request->name]);

        return redirect()->route('superadmin.roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }
}
