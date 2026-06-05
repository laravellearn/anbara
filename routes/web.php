<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Core\{
    UserController,
    ProfileController,
    CompanySwitcherController,
    FiscalYearSwitcherController,
    BillingController,
    FiscalYearController,
    CompanyController,
    ActivityLogController
};

use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\{
    DashboardController
};



//Authentication-----------------
Route::middleware(['guest'])->group(function () {

    //Login-----------------
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    // صفحه وارد کردن موبایل برای دریافت OTP
    Route::get('/login/otp/request', [LoginController::class, 'showOtpRequestForm'])->name('login.otp.request');
    // ارسال کد OTP
    Route::post('/login/otp/send', [LoginController::class, 'sendOtp'])->name('login.otp.send')->middleware('throttle:3,1');
    // صفحه وارد کردن کد تأیید
    Route::get('/login/otp/verify', [LoginController::class, 'showOtpVerifyForm'])->name('login.otp.verify');
    // تأیید کد
    Route::post('/login/otp/verify', [LoginController::class, 'verifyOtp'])->name('login.otp.verify.post');
    // ارسال مجدد کد
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend')->middleware('throttle:3,1');

    //Register-----------------
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    // ارسال کد OTP - ثبت نام
    Route::get('/register/otp/request', [RegisterController::class, 'showOtpRequestForm'])->name('register.otp.form');
    Route::post('/register/otp/send', [RegisterController::class, 'sendOtp'])->name('register.otp.send')->middleware('throttle:3,1');
    Route::post('/register/otp/verify', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/register/otp/resend', [RegisterController::class, 'resendOtp'])->name('register.otp.resend')->middleware('throttle:3,1');

    //Forget Password-----------------
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.send')->middleware('throttle:3,1');
    Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend')->middleware('throttle:3,1');
    Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // خروج
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

// همهٔ مسیرهایی که نیاز به tenant دارند
Route::middleware(['auth', 'require.tenant'])->group(function () {
    Route::post('/switch-company', [CompanySwitcherController::class, 'switch'])->name('company.switch');
    Route::post('/switch-fiscal-year', [FiscalYearSwitcherController::class, 'switch'])->name('fiscal-year.switch');
    //مدیریت اشتراک ها
    Route::get('/billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/license', [BillingController::class, 'license'])->name('billing.license');
    Route::get('/billing/history', [BillingController::class, 'history'])->name('billing.history');

    //لاگ های سیستمی
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    //سال مالی و سازمان
    // در routes/web.php (گروه admin)
    Route::get('/companies/manage', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/fiscal-years/manage', [FiscalYearController::class, 'index'])->name('fiscal-years.index');

    // مدیریت کاربران
    Route::resource('users', UserController::class)->except(['show']);
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    // پروفایل
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
});
