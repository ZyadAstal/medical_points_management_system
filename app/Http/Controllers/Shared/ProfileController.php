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

        // All roles now use the shared profile view
        $view = 'shared.profile';

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
        $role = $user->role->name;
        
        $request->validate([
            'name' => $role === 'SuperAdmin' ? 'required|string|max:255' : 'nullable',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $updateData = ['email' => $request->email];

        if ($role === 'SuperAdmin') {
            $updateData['name'] = $request->name;
        }

        $user->update($updateData);

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
