<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\OtpCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordVerifyController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = session('reset_password_user_id');

        $user = User::findOrFail($userId);

        $otp = OtpCode::query()

            ->where('user_id', $user->id)

            ->where('is_used', false)

            ->latest()

            ->first();

        if (!$otp) {

            return back()->withErrors([
                'otp' => 'کد نامعتبر است'
            ]);
        }

        if ($otp->attempts >= 5) {

            return back()->withErrors([
                'otp' => 'تعداد دفعات مجاز به پایان رسیده است'
            ]);
        }

        if ($otp->expires_at->isPast()) {

            return back()->withErrors([
                'otp' => 'کد منقضی شده است'
            ]);
        }

        $otp->increment('attempts');

        if (
            !Hash::check(
                $request->otp,
                $otp->code
            )
        ) {

            return back()->withErrors([
                'otp' => 'کد تایید صحیح نیست'
            ]);
        }

        $otp->update([
            'is_used' => true
        ]);

        session([
            'password_reset_verified' => true
        ]);

        return redirect()->route(
            'password.reset.form'
        );
    }
}