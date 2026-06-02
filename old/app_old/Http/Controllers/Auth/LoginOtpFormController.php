<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class LoginOtpFormController extends Controller
{
    public function __invoke()
    {
        return view('auth.login-otp');
    }
}