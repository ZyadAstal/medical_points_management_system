<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->authenticated(request(), Auth::user());
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        // محاولة تسجيل الدخول
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return $this->authenticated($request, Auth::user());
        }

        throw ValidationException::withMessages([
            'username' => ['بيانات الدخول غير صحيحة.'],
        ]);
    }

    protected function authenticated(Request $request, $user)
    {
        // توجيه المستخدم حسب دوره
        $role = $user->role->name;

        switch ($role) {
            case 'Super Admin':
            case 'SuperAdmin':
                return redirect()->intended(route('superadmin.dashboard'));
            
            case 'Doctor':
                return redirect()->intended(route('doctor.dashboard'));
            
            case 'Pharmacist':
                return redirect()->intended(route('pharmacist.dashboard'));
            
            case 'Patient':
                return redirect()->intended(route('patient.dashboard'));

            case 'Reception':
                return redirect()->intended(route('reception.dashboard'));

            case 'CenterManager':
                return redirect()->intended(route('manager.dashboard'));

            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'ليس لديك صلاحية للدخول.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
