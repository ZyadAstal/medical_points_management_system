<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\MedicalCenter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    // عرض قائمة المستخدمين (أطباء، صيادلة، استقبال)
    public function index(Request $request)
    {
        $query = User::with(['role', 'medicalCenter'])
            ->whereHas('role', function($q) {
                $q->whereNotIn('name', ['Super Admin', 'Patient']);
            });

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Role Filter
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                // Handle both ID and Name if needed, but here we expect name
                if (is_numeric($request->role)) {
                    $q->where('id', $request->role);
                } else {
                    $q->where('name', $request->role);
                }
            });
        }

        // Medical Center Filter
        if ($request->filled('center')) {
            $query->where('medical_center_id', $request->center);
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::whereNotIn('name', ['Super Admin', 'Patient'])->get();
        $centers = MedicalCenter::all();

        return view('superadmin.users', compact('users', 'roles', 'centers'));
    }

    // صفحة إنشاء مستخدم جديد
    public function create()
    {
        $roles = Role::whereNotIn('name', ['Super Admin', 'Patient'])->get();
        $centers = MedicalCenter::all();
        
        return view('superadmin.users.create', compact('roles', 'centers'));
    }

    // حفظ المستخدم الجديد
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
            'medical_center_id' => 'nullable|exists:medical_centers,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'medical_center_id' => $request->medical_center_id,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'تم إنشاء المستخدم بنجاح');
    }

    // صفحة تعديل بيانات المستخدم
    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', ['Super Admin', 'Patient'])->get();
        $centers = MedicalCenter::all();
        
        return view('superadmin.users.edit', compact('user', 'roles', 'centers'));
    }

    // تحديث بيانات المستخدم
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role_id' => 'required|exists:roles,id',
            'medical_center_id' => 'nullable|exists:medical_centers,id',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'medical_center_id' => $request->medical_center_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('superadmin.users.index')->with('success', 'تم تحديث بيانات المستخدم بنجاح');
    }

    // حذف المستخدم
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي');
        }
        
        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success', 'تم حذف المستخدم بنجاح');
    }
}
