<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Models\OtpCode;
use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;

class ResendOtpController extends Controller
{
    public function __invoke()
    {
        $userId = session('pending_user_id');
        $user = User::findOrFail($userId);

        $lastOtp = OtpCode::query()
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (
            $lastOtp &&
            $lastOtp->created_at->gt(
                now()->subMinutes(2)
            )
        ) {

            return back()
                ->withErrors([
                    'code' => 'تا پایان زمان کد فعلی امکان ارسال مجدد وجود ندارد.'
                ]);
        }

        $code = app(OtpService::class)->generate($user);

        app(IPPanelService::class)->sendOtp(
            $user->mobile,
            $code
        );
        
        return back()
            ->with(
                'success',
                'کد تایید جدید ارسال شد.'
            );
    }
}
