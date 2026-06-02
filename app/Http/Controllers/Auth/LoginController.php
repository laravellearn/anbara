<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\OtpCode;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // نمایش فرم اصلی لاگین (موبایل + رمز عبور)
    public function showLoginForm()
    {
        return view('auth.login'); // Blade آماده تو
    }

    // لاگین با رمز عبور
    public function login(LoginRequest $request)
    {

        $user = $this->authService->loginWithPassword($request->mobile, $request->password);

        $credentials = [
            'mobile' => $request->mobile,
            'password' => $request->password,
            'is_active' => true,
        ];

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {

            return back()
                ->withInput()
                ->withErrors([
                    'mobile' => 'اطلاعات ورود صحیح نیست.'
                ]);
        }
        auth()->user()->update([
            'last_login_at' => now(),
            'last_ip' => now()
        ]);

        Auth::login($user, $request->filled('remember'));

        return redirect()->intended('/dashboard');
    }

    // ==================== صفحه وارد کردن موبایل برای OTP ====================
    public function showOtpRequestForm()
    {
        return view('auth.otp-request');   // Blade جدید
    }

    // ==================== ارسال کد OTP ====================
    public function sendOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|digits:11|exists:users,mobile',
        ]);

        $sent = $this->authService->sendLoginOtp($request->mobile);

        if (!$sent) {
            return back()->withErrors(['mobile' => 'ارسال کد با مشکل مواجه شد. لطفاً دوباره تلاش کنید.']);
        }

        // ذخیره موبایل در سشن (امن)
        session(['login_otp_mobile' => $request->mobile]);

        return redirect()->route('login.otp.verify')
            ->with('success', 'کد تأیید به شماره شما ارسال شد.');
    }

    // نمایش صفحه وارد کردن کد
    public function showOtpVerifyForm()
    {
        $mobile = session('login_otp_mobile');

        if (!$mobile) {
            return redirect()->route('login.otp.request')
                ->withErrors(['mobile' => 'لطفاً شماره موبایل خود را وارد کنید.']);
        }

        // پیدا کردن OTP فعال برای محاسبه زمان باقی‌مانده
        $otp = OtpCode::where('mobile', $mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->first();

        $expiresAt = $otp ? $otp->expires_at : now()->addMinutes(2);

        return view('auth.otp-verify', [
            'mobile' => $mobile,
            'expiresAt' => $expiresAt
        ]);
    }

    // تأیید کد
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $mobile = session('login_otp_mobile');

        if (!$mobile) {
            return redirect()->route('login.otp.request')
                ->withErrors(['error' => 'جلسه شما منقضی شده است.']);
        }

        $result = $this->authService->verifyLoginOtp($mobile, $request->code);

        if (!$result) {
            return back()->withErrors(['code' => 'کد وارد شده اشتباه یا منقضی شده است.']);
        }

        session()->forget('login_otp_mobile');

        Auth::login($result['user']);

        return redirect()->intended('/dashboard')
            ->with('success', 'با موفقیت وارد شدید.');
    }

    // ... متدهای قبلی (showLoginForm, showOtpRequestForm, sendOtp, showOtpVerifyForm, verifyOtp) ...

    /**
     * ارسال مجدد کد OTP
     */
    public function resendOtp(Request $request)
    {
        $mobile = session('login_otp_mobile');

        if (!$mobile) {
            return response()->json([
                'success' => false,
                'message' => 'جلسه شما منقضی شده است. لطفاً دوباره از ابتدا شروع کنید.'
            ], 400);
        }

        $sent = $this->authService->sendLoginOtp($mobile);

        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'ارسال مجدد کد با مشکل مواجه شد.'
            ], 500);
        }

        // بروزرسانی زمان انقضا در سشن (اختیاری)
        return response()->json([
            'success' => true,
            'message' => 'کد جدید ارسال شد.'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}