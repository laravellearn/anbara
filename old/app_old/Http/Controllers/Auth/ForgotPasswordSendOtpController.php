<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Http\Request;

class ForgotPasswordSendOtpController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'mobile' => [
                'required',
                'regex:/^09[0-9]{9}$/'
            ]
        ]);

        $user = User::where(
            'mobile',
            $request->mobile
        )->first();

        if (!$user) {

            return back()->withErrors([
                'mobile' => 'کاربری با این شماره یافت نشد.'
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
            'reset_password_user_id' => $user->id
        ]);

        return redirect()->route(
            'password.verify.form'
        );
    }
}