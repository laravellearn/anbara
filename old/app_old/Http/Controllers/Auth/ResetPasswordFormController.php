<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class ResetPasswordFormController extends Controller
{
    public function __invoke()
    {
        abort_unless(
            session('password_reset_verified'),
            403
        );

        return view(
            'auth.reset-password'
        );
    }
}