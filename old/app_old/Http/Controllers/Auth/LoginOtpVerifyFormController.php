<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;

class LoginOtpVerifyFormController extends Controller
{
    public function __invoke()
    {
        $userId = session('login_otp_user_id');

        abort_unless($userId, 404);

        $user = User::findOrFail($userId);

        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->latest()
            ->first();

        abort_unless($otp, 404);

        return view(
            'auth.login-otp-verify',
            [
                'user' => $user,
                'expiresAt' => $otp->expires_at,
            ]
        );
    }
}