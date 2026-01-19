<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MedicalCenter;
use App\Models\Prescription;
use App\Models\Dispense;

class SuperAdminController extends Controller
{
    /**
     * Dashboard - إحصائيات عامة عن النظام
     */
    public function dashboard()
    {
        return response()->json([
            'total_users'         => User::count(),
            'total_medical_centers' => MedicalCenter::count(),
            'total_prescriptions' => Prescription::count(),
            'total_dispenses'     => Dispense::count(),
        ]);
    }

    /**
     * عرض جميع المستخدمين
     */
    public function users()
    {
        return response()->json(
            User::with('role')->get()
        );
    }

    /**
     * إنشاء مستخدم جديد
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'role_id'  => $request->role_id,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => $user
        ]);
    }

    /**
     * تفعيل / تعطيل مستخدم
     */
    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json([
            'message' => 'User status updated',
            'status'  => $user->is_active ? 'Active' : 'Inactive'
        ]);
    }

    /**
     * عرض جميع المراكز الطبية
     */
    public function medicalCenters()
    {
        return response()->json(
            MedicalCenter::all()
        );
    }

    /**
     * إنشاء مركز طبي جديد
     */
    public function storeMedicalCenter(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:medical_centers',
        ]);

        $center = MedicalCenter::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'message' => 'Medical center created successfully',
            'center'  => $center
        ]);
    }
}
