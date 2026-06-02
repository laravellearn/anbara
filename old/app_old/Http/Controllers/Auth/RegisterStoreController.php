<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;

class RegisterStoreController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make(
                $request->password
            ),
        ]);

        $code = app(OtpService::class)->generate($user);

        app(IPPanelService::class)->sendOtp(
            $user->mobile,
            $code
        );

        session([
            'pending_user_id' => $user->id
        ]);
        

        return redirect()->route('otp.form');   
     }
}
