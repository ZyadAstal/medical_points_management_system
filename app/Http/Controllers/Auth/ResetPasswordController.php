<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordController extends Controller
{
    public function showDirectResetForm()
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request')->with('error', 'يرجى إدخال بريدك الإلكتروني أولاً.');
        }

        return view('auth.passwords.reset_direct', ['email' => session('reset_email')]);
    }

    public function resetDirectly(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.confirmed' => 'كلمة المرور غير متطابقة.',
            'password.min' => 'يجب أن لا تقل كلمة المرور عن 8 أحرف.',
            'email.exists' => 'البريد الإلكتروني غير مسجل.',
        ]);

        if (session('reset_email') !== $request->email) {
            return redirect()->route('password.request')->with('error', 'حدث خطأ في العملية. يرجى المحاولة مرة أخرى.');
        }

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();

            session()->forget('reset_email');

            return redirect()->route('login')->with('success', 'تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.');
        }

        return back()->with('error', 'حدث خطأ. يرجى المحاولة مرة أخرى.');
    }
}
