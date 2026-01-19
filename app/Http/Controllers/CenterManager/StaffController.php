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
    public function index()
    {
        $centerId = Auth::user()->medical_center_id;
        
        $staff = User::with('role')
            ->where('medical_center_id', $centerId)
            ->whereHas('role', function($q) {
                $q->whereIn('name', ['Doctor', 'Pharmacist', 'Reception']);
            })
            ->get();

        return view('manager.staff.index', compact('staff'));
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

    public function edit(User $user)
    {
        // التأكد من أن الموظف يتبع لنفس المركز
        if ($user->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }

        $roles = Role::whereIn('name', ['Doctor', 'Pharmacist', 'Reception'])->get();
        return view('manager.staff.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
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

        $user->update($data);

        return redirect()->route('manager.staff.index')->with('success', 'تم تحديث بيانات الموظف بنجاح');
    }

    public function destroy(User $user)
    {
        if ($user->medical_center_id !== Auth::user()->medical_center_id) {
            abort(403);
        }
        
        $user->delete();
        return redirect()->route('manager.staff.index')->with('success', 'تم حذف الموظف بنجاح');
    }
}
