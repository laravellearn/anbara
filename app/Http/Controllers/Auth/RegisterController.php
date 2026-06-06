<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Services\Sms\IPPanelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\OtpCode;
use Illuminate\Http\JsonResponse;
use App\Services\Auth\AuthService;
use App\Services\TenantManager;
use App\Services\TenantRegistrationService;
use Illuminate\Support\Facades\RateLimiter;

class RegisterController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();

        try {

            $user = User::where(
                'mobile',
                $request->mobile
            )->first();

            // کاربر قبلاً ثبت نام کرده و تایید شده
            if (
                $user &&
                $user->mobile_verified_at
            ) {

                return back()
                    ->withInput()
                    ->withErrors([
                        'mobile' => 'این شماره موبایل قبلاً ثبت شده است.'
                    ]);
            }

            // کاربر وجود ندارد
            if (!$user) {

                $user = User::create([
                    'name' => $request->name,
                    'mobile' => $request->mobile,
                    'password' => Hash::make(
                        $request->password
                    ),
                    'is_active' => false,
                ]);
            }

            // اگر وجود دارد ولی تایید نشده
            else {

                $user->update([
                    'name' => $request->name,
                    'password' => Hash::make(
                        $request->password
                    ),
                ]);
            }

            $code = app(OtpService::class)
                ->generate($user);

            $sent = app(IPPanelService::class)
                ->sendOtp(
                    $user->mobile,
                    $code
                );


            if (!$sent) {

                DB::rollBack();

                return back()
                    ->withInput()
                    ->withErrors([
                        'mobile' => 'خطا در ارسال پیامک. مجدداً تلاش کنید.'
                    ]);
            }

            session([
                'pending_user_id' => $user->id
            ]);

            session([
                'registration_data' => [
                    'organization_name' => $request->organization_name,
                    'slug'              => $request->slug,
                    'email'             => $request->email,
                    'phone'             => $request->phone,
                ],
                'pending_user_id'    => $user->id,
            ]);

            DB::commit();

            return redirect()
                ->route('register.otp.form');
        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'mobile' => 'خطایی رخ داده است.'
                ]);
        }
    }
    public function showOtpRequestForm()
    {
        $userId = session('pending_user_id');

        if (!$userId) {
            return redirect()->route('register')
                ->withErrors([
                    'error' => 'ابتدا ثبت نام را انجام دهید.'
                ]);
        }

        $user = User::find($userId);

        if (!$user) {

            session()->forget('pending_user_id');

            return redirect()->route('register')
                ->withErrors([
                    'error' => 'کاربر یافت نشد.'
                ]);
        }

        $otp = $user->otpCodes()
            ->latest()
            ->first();

        if (!$otp) {
            return redirect()->route('register')
                ->withErrors([
                    'error' => 'کد تاییدی برای این کاربر یافت نشد.'
                ]);
        }

        return view('auth.register-otp-verify', [
            'user' => $user,
            'expiresAt' => $otp->expires_at,
        ]);
    }

    public function sendOtp(Request $request)
    {
        $userId = session('pending_user_id');
        $user = User::findOrFail($userId);

        $key = 'register-otp-send:' . $user->mobile;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'mobile' => "لطفاً {$seconds} ثانیه دیگر تلاش کنید."
            ]);
        }
        RateLimiter::hit($key, 120);

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

    public function resendOtp(
        OtpService $otpService,
        IPPanelService $smsService
    ): JsonResponse {
    
        $userId = session('pending_user_id');
    
        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'نشست شما منقضی شده است.'
            ], 422);
        }
    
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'کاربر یافت نشد.'
            ], 404);
        }
    
        // جلوگیری از اسپم با بررسی آخرین OTP (قبل از تولید کد جدید)
        $lastOtp = $user->otpCodes()->latest()->first();
    
        if ($lastOtp && now()->lt($lastOtp->expires_at)) {
            return response()->json([
                'success' => false,
                'message' => 'هنوز امکان ارسال مجدد کد وجود ندارد.'
            ], 429);
        }
    
        // تولید و ارسال کد جدید
        $code = $otpService->generate($user);
        $sent = $smsService->sendOtp($user->mobile, $code);
    
        if (!$sent) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ارسال پیامک.'
            ], 500);
        }
    
        // دریافت آخرین OTP (که الان همان OTP جدید است)
        $newOtp = $user->otpCodes()->latest()->first();
    
        return response()->json([
            'success'    => true,
            'message'    => 'کد تایید مجدداً ارسال شد.',
            'expires_at' => $newOtp->expires_at->timestamp,
        ]);
    }

    
    public function verifyOtp(Request $request, TenantRegistrationService $registrationService)
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = session('pending_user_id');

        if (! $userId) {
            return redirect()->route('register.otp.form')
                ->withErrors(['error' => 'جلسه شما منقضی شده است.']);
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget('pending_user_id');

            return redirect()->route('register')
                ->withErrors(['error' => 'کاربر یافت نشد.']);
        }

        // اعتبارسنجی کد OTP
        $result = $this->authService->verifyRegisterOtp(
            $user->mobile,
            $request->code
        );

        if (! $result) {
            $otp = OtpCode::where('mobile', $user->mobile)
                ->latest()
                ->first();

            if ($otp && $otp->attempts >= 5) {
                return back()->withErrors([
                    'code' => 'تعداد دفعات مجاز وارد کردن کد به پایان رسیده است. لطفاً کد جدید دریافت کنید.',
                ]);
            }

            return back()->withErrors([
                'code' => 'کد وارد شده اشتباه یا منقضی شده است.',
            ]);
        }

        // ========== بخش جدید: ایجاد Tenant اگر کاربر جدید است ==========
        if (! $user->tenant_id) {
            $registrationData = session('registration_data');

            if (! $registrationData) {
                // اطلاعات سازمان در سشن موجود نیست – کاربر را به مرحلهٔ اول برگردان
                session()->forget('pending_user_id');
                return redirect()->route('register')
                    ->withErrors(['error' => 'اطلاعات ثبت‌ نام اولیه یافت نشد. لطفاً دوباره تلاش کنید.']);
            }

            // ایجاد Tenant، شرکت، نقش، اشتراک و …
            $result = $registrationService->finalizeRegistration($user, $registrationData);
            $tenant  = $result['tenant'];
            $company = $result['company'];

            // پاک کردن داده‌های موقت ثبت‌نام
            session()->forget('registration_data');
        } else {
            // کاربر قبلاً Tenant دارد (احراز هویت دوباره، ورود با OTP)
            $tenant  = $user->tenant;
            $company = $user->defaultCompany(); // متد زیر را به مدل User اضافه کنید
        }

        // فعال‌سازی کاربر
        $user->update([
            'mobile_verified_at' => now(),
            'is_active'          => true,
        ]);

        session()->forget('pending_user_id');

        // ورود کاربر
        Auth::login($user);
        $request->session()->regenerate();

        // تنظیم Context چندمستأجری
        $manager = app(TenantManager::class);
        $manager->setTenant($tenant);
        $manager->setCompany($company);
        $manager->setUser($user);
        session(['current_company_id' => $company->id]);

        return redirect()->route('dashboard')
            ->with('success', 'ثبت‌ نام شما با موفقیت تکمیل شد. خوش آمدید!');
    }
}
