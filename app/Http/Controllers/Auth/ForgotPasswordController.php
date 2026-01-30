<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        // In a real production environment, we would use:
        // $status = Password::sendResetLink($request->only('email'));
        
        // For this local setup, to avoid SMTP errors:
        return back()->with('status', 'نظام استعادة كلمة المرور غير مفعل بالكامل في البيئة المحلية. يرجى مراجعة إدارة النظام لتغيير كلمة المرور.');
    }
}
