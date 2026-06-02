<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    /**
     * تولید و ذخیره کد OTP
     */
    public function generate(User $user): string
    {
        // حذف کدهای قبلی این کاربر
        OtpCode::where('user_id', $user->id)->delete();

        $code = app()->environment('local')
            ? env('OTP_FIXED_CODE', '123456')
            : (string) random_int(100000, 999999);

        OtpCode::create([
            'user_id'    => $user->id,
            'mobile'     => $user->mobile,
            'code'       => Hash::make($code),        // هش شده ذخیره می‌شود
            'expires_at' => now()->addMinutes(2),
            'ip'         => request()->ip(),
            'attempts'   => 0,
        ]);

        return $code; // کد خام برای ارسال پیامک
    }

    /**
     * بررسی اعتبار کد OTP
     */
    public function verify(string $mobile, string $inputCode): ?User
    {
        $otp = OtpCode::where('mobile', $mobile)
            ->where('expires_at', '>', now())
            ->where('is_used', false)
            ->first();

        if (!$otp) {
            return null;
        }

        // افزایش تعداد تلاش
        $otp->increment('attempts');

        if (!Hash::check($inputCode, $otp->code)) {
            return null;
        }

        // موفق بود
        $otp->update(['is_used' => true]);

        return User::find($otp->user_id);
    }
}