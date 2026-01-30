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
        
        $query = Role::withCount('users');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('role')) {
            $query->where('name', $request->role);
        }

        $roles = $query->paginate(10)->withQueryString();
        return view('superadmin.roles', compact('roles', 'allRoles'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
        ]);

        // Prevent changing core system roles if necessary, but for now allow editing names or description if added
        // The design only shows Name and User Count (readonly usually). 
        // If the user wants to rename a role, we update it.
        
        $role->update(['name' => $request->name]);

        return redirect()->route('superadmin.roles.index')->with('success', 'تم تحديث الدور بنجاح');
    }
}
