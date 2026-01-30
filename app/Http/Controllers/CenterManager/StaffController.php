<?php

namespace App\Http\Controllers\CenterManager;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $centerId = Auth::user()->medical_center_id;
        
        $query = User::with('role')
            ->where('medical_center_id', $centerId)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['Doctor', 'Pharmacist', 'Reception']);
            });

        // Search Filter (by name, email, or username)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        $staff = $query->paginate(10)->withQueryString();
        
        $availableRoles = Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get();

        return view('manager.employee', compact('staff', 'availableRoles'));
    }

    public function create()
    {
        $roles = Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get();
        return view('manager.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $centerId = Auth::user()->medical_center_id;

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'medical_center_id' => $centerId,
        ]);

        return redirect()->route('manager.staff.index')->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function edit(User $staff)
    {
        // التأكد من أن الموظف يتبع لنفس المركز
        if ($staff->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }

        $roles = Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get();
        return view('manager.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $staff->id,
            'email' => 'required|email|unique:users,email,' . $staff->id,
            'password' => 'nullable|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $staff->update($data);

        return redirect()->route('manager.staff.index')->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(User $staff)
    {
        if ($staff->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }
        
        $staff->delete();
        return redirect()->route('manager.staff.index')->with('success', 'تم حذف الموظف بنجاح');
    }
}
