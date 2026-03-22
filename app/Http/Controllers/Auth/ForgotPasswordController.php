<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            session(['reset_email' => $user->email]);
            return redirect()->route('password.reset_direct')->with('success', 'البريد الإلكتروني صحيح. يرجى إدخال كلمة المرور الجديدة.');
        }

        return back()->with('error', 'البريد الإلكتروني غير مسجل في النظام.');
    }
}
