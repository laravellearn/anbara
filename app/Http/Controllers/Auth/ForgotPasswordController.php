<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(
        Request $request,
        OtpService $otpService,
        IPPanelService $smsService
    ) {
        $request->validate([
            'mobile' => ['required', 'digits:11']
        ]);

        $user = User::where('mobile', $request->mobile)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'mobile' => 'کاربری با این شماره همراه یافت نشد.'
            ]);
        }

        $code = $otpService->generate($user);

        $smsService->sendOtp(
            $user->mobile,
            $code
        );

        session([
            'password_reset_user_id' => $user->id
        ]);

        return redirect()
            ->route('password.otp.form');
    }

    public function showOtpForm()
    {
        $userId = session('password_reset_user_id');

        if (!$userId) {
            return redirect()
                ->route('password.request');
        }

        $user = User::findOrFail($userId);

        $otp = $user->otpCodes()
            ->latest()
            ->first();

        return view('auth.forgot-password-otp', [
            'user' => $user,
            'expiresAt' => $otp->expires_at,
        ]);
    }

    public function verifyOtp(
        Request $request,
        OtpService $otpService
    ) {
        $request->validate([
            'code' => ['required', 'digits:6']
        ]);

        $userId = session('password_reset_user_id');

        if (!$userId) {
            return redirect()
                ->route('password.request');
        }

        $user = User::findOrFail($userId);

        $verified = $otpService->verify(
            $user->mobile,
            $request->code
        );

        if (!$verified) {
            return back()->withErrors([
                'code' => 'کد وارد شده صحیح نیست.'
            ]);
        }

        session([
            'password_reset_verified' => true
        ]);

        return redirect()
            ->route('password.reset.form');
    }

    public function resendOtp(
        OtpService $otpService,
        IPPanelService $smsService
    ) {
        $userId = session('password_reset_user_id');

        if (!$userId) {

            return response()->json([
                'success' => false,
                'message' => 'نشست شما منقضی شده است.'
            ], 422);
        }

        $user = User::find($userId);

        $code = $otpService->generate($user);

        $smsService->sendOtp(
            $user->mobile,
            $code
        );

        return response()->json([
            'success' => true,
            'message' => 'کد تایید مجددا ارسال شد.',
            'expires_at' => now()->addMinutes(2)->timestamp
        ]);
    }

    public function showResetForm()
    {
        if (
            !session('password_reset_verified')
        ) {
            return redirect()
                ->route('password.request');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(
        ForgotPasswordRequest $request
    ) {

        $userId = session('password_reset_user_id');

        if (!$userId) {
            return redirect()
                ->route('password.request');
        }

        $user = User::findOrFail($userId);

        $user->update([
            'password' => Hash::make(
                $request->password
            )
        ]);

        session()->forget([
            'password_reset_user_id',
            'password_reset_verified'
        ]);

        Auth::login($user);

        return redirect()
            ->route('dashboard');
    }

}
