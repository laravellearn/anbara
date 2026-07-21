<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Bus;

class AuthService
{
    protected OtpService $otpService;
    protected IPPanelService $smsService;

    public function __construct(OtpService $otpService, IPPanelService $smsService)
    {
        $this->otpService = $otpService;
        $this->smsService = $smsService;
    }

    // ==================== لاگین با رمز عبور ====================
    public function loginWithPassword(string $mobile, string $password): ?User
    {
        $user = User::where('mobile', $mobile)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            return null; // کاربر غیرفعال
        }

        $user->update([
            'last_login_at' => now(),
            'last_ip' => request()->ip()
        ]);

        return $user;
    }

    /**
     * ارسال کد یکبار مصرف برای ورود
     */
    public function sendLoginOtp(string $mobile): bool
    {
        $user = User::where('mobile', $mobile)->first();

        if (!$user) {
            return false;
        }

        // کاربران غیرفعال نمی‌توانند با OTP وارد شوند — فعال‌سازی خودکار ممنوع
        if ($user->is_active === false) {
            return false;
        }

        $code = $this->otpService->generate($user);

        dispatch(function () use ($user, $code) {
            app(\App\Services\Sms\IPPanelService::class)
                ->sendOtp($user->mobile, $code);
        })->afterResponse();

        return true;
    }    /**
     * تأیید کد یکبار مصرف
     */
    public function verifyLoginOtp(string $mobile, string $code): ?User
    {
        $user = $this->otpService->verify($mobile, $code);

        if (!$user) {
            return null;
        }

        // به‌روزرسانی اطلاعات آخرین ورود
        $user->update([
            'last_login_at' => now(),
            'last_ip' => request()->ip(),
        ]);

        return $user;
    }

    public function verifyRegisterOtp(string $mobile, string $code): ?User
    {
        $user = $this->otpService->verify($mobile, $code);
    
        if (!$user) {
            return null;
        }
    
        $user->update([
            'last_login_at' => now(),
            'last_ip' => request()->ip(),
        ]);
    
        return $user;
    }
}