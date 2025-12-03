<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomAuthenticatedSessionController extends Controller
{
    public function destroy(Request $request)
    {
        $isAdmin = $request->session()->get('is_admin', false);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $isAdmin
            ? redirect()->route('admin.login')
            : redirect()->route('login');
    }
}
