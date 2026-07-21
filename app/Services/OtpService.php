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
        // حذف OTP های قبلی
        OtpCode::where('user_id', $user->id)->delete();

        $code = app()->environment('local')
            ? env('OTP_FIXED_CODE', '123456')
            : (string) random_int(100000, 999999);

        OtpCode::create([
            'user_id'    => $user->id,
            'mobile'     => $user->mobile,
            'code'       => Hash::make($code),
            'expires_at' => now()->addMinutes(2),
            'ip'         => request()->ip(),
            'attempts'   => 0,
            'is_used'    => false,
        ]);

        return $code;
    }

    /**
     * بررسی اعتبار OTP
     */
    public function verify(string $mobile, string $inputCode): ?User
    {
        $otp = OtpCode::where('mobile', $mobile)
            ->where('is_used', false)
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (!$otp) {
            return null;
        }

        // بیش از حد مجاز تلاش کرده
        if ($otp->attempts >= 5) {

            $otp->update([
                'is_used' => true
            ]);

            return null;
        }

        // کد اشتباه
        if (!Hash::check($inputCode, $otp->code)) {

            $otp->increment('attempts');

            // بعد از پنجمین خطا OTP باطل شود
            if ($otp->attempts >= 5) {
                $otp->update([
                    'is_used' => true
                ]);
            }

            return null;
        }

        // موفق
        $otp->update([
            'is_used' => true,
        ]);

        return User::find($otp->user_id);
    }
}