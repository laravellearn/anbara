<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class ForgotPasswordVerifyFormController extends Controller
{
    public function __invoke()
    {
        $userId = session('reset_password_user_id');

        abort_unless($userId, 404);

        $user = User::findOrFail($userId);

        $otp = $user->otp;

        return view(
            'auth.otp-forgot',
            [
                'user' => $user,
                'expiresAt' => $otp->expires_at,
                'action' => route('password.verify'),
            ]
        );
    }
}