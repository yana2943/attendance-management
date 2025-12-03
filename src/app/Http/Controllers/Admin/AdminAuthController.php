<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Requests\CustomLoginRequest;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(CustomLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            session(['is_admin' => true]);

            return redirect()->route('admin.attendance.list');
        }

        return back()->withErrors(['email' => 'メールアドレスかパスワードが違います']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget('is_admin');
        return redirect()->route('admin.login');
    }
}