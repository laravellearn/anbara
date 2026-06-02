<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\OtpCode;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
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
        

        return redirect()->route('register.otp.form');   
    }

    public function showOtpRequestForm()
    {
        $userId = session('pending_user_id');

        abort_unless($userId, 403);
    
        $user = User::findOrFail($userId);
    
        return view('auth.register-otp-verify', [
            'user' => $user,
            'expiresAt' => $user->otp->expires_at,
        ]);
    }

    public function sendOtp(Request $request)
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
