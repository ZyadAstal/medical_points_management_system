<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $role = $user->role->name;

        // Determine view based on role
        $view = match($role) {
            'SuperAdmin' => 'superadmin.profile',
            'CenterManager' => 'manager.profile',
            default => 'shared.profile'
        };

        // Determine layout based on role (for shared.profile if used)
        $layout = match($role) {
            'SuperAdmin' => 'layouts.admin',
            'CenterManager' => 'layouts.manager',
            'Doctor' => 'layouts.doctor',
            'Pharmacist' => 'layouts.pharmacist',
            'Reception' => 'layouts.reception',
            'Patient' => 'layouts.patient',
            default => 'layouts.app'
        };

        return view($view, compact('user', 'layout', 'role'));
    }

    public function updatePersonal(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'تم تحديث البيانات الشخصية بنجاح');
    }

    public function updateSecurity(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح');
    }
}
