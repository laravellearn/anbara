<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class VerifyOtpController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = session('pending_user_id');
        $user = User::findOrFail($userId);

        $otp = OtpCode::query()
            ->where('user_id', $user->id)
            ->where('is_used', false)
            ->latest()
            ->first();

            if (
                !$otp ||
                !Hash::check(
                    $request->otp,
                    $otp->code
                )
            ) {
                return back()->withErrors([
                    'otp' => 'کد تایید صحیح نیست.'
                ]);
            }
            if (!$otp) {
            return back()
                ->withErrors([
                    'code' => 'کد نامعتبر است'
                ]);
        }

        if ($otp->expires_at->isPast()) {
            return back()
                ->withErrors([
                    'code' => 'کد منقضی شده است'
                ]);
        }

        $otp->update([
            'is_used' => true
        ]);

        $user->update([
            'mobile_verified_at' => now(),
            'is_active' => true,
            'last_login_at' => now(),
            'last_ip' => $request->ip(),
        ]);

        Auth::login($user);

        session()->regenerate();

        return redirect('dashboard');
    }
}
