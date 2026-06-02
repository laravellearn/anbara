<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginOtpVerifyController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = session(
            'login_otp_user_id'
        );

        $user = User::find($userId);

        if (!$user) {
            return redirect()
                ->route('login');
        }

        $otp = OtpCode::query()

            ->where('user_id', $user->id)

            ->where('is_used', false)

            ->latest()

            ->first();

        if (!$otp) {

            return back()
                ->withErrors([
                    'otp' => 'کد نامعتبر است'
                ]);
        }

        if ($otp->expires_at->isPast()) {

            return back()
                ->withErrors([
                    'otp' => 'کد منقضی شده است'
                ]);
        }

        if (
            ! Hash::check(
                $request->otp,
                $otp->code
            )
        ) {

            return back()
                ->withErrors([
                    'otp' => 'کد تایید صحیح نیست'
                ]);
        }

        $otp->update([
            'is_used' => true
        ]);

        Auth::login($user);

        session()->forget(
            'login_otp_user_id'
        );

        $request->session()
            ->regenerate();

        return redirect()
            ->intended('/dashboard');
    }
}