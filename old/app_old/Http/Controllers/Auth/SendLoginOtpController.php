<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Http\Request;

class SendLoginOtpController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'mobile' => ['required']
        ]);

        $user = User::where(
            'mobile',
            $request->mobile
        )->first();

        if (!$user) {

            return back()->withErrors([
                'mobile' => 'کاربری با این شماره یافت نشد'
            ]);
        }

        $code = app(OtpService::class)
            ->generate($user);

        app(IPPanelService::class)
            ->sendOtp(
                $user->mobile,
                $code
            );

        session([
            'login_otp_user_id' => $user->id
        ]);

        return redirect()->route(
            'login.otp.verify.form'
        );
    }
}