<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function attendance() {
        return view('attendance');
    }

    public function showVerifyEmail(Request $request)
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $request->user()->id, 'hash' => sha1($request->user()->email)]
        );

        return view('auth.verify-email', compact('verificationUrl'));
    }
}
