<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\superAdmin\{
    SuperDashboardController,
    SuperTenantController,
    SuperPlanController,
    SuperSubscriptionController,
    SuperActivityLogController,
    SuperAdminRoleController,
    ToolController,
    SuperUserController
};


use App\Http\Controllers\Core\{
    UserController,
    ProfileController,
    CompanySwitcherController,
    FiscalYearSwitcherController,
    BillingController,
    FiscalYearController,
    CompanyController,
    ActivityLogController,
    RoleController,
    PermissionController
};

use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use App\Http\Controllers\{
    DashboardController
};


Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'superadmin'])->group(function () {
    // چک Super Admin در یک Middleware اختصاصی (یا با استفاده از Controller)
    Route::get('/', [SuperDashboardController::class, 'index'])->name('dashboard');

    // مدیریت Tenantها
    Route::resource('tenants', SuperTenantController::class)->except(['show']);
    Route::resource('users', SuperUserController::class)->except(['show']);

    // مدیریت پلن‌ها
    Route::resource('plans', SuperPlanController::class)->except(['show']);

    // مدیریت اشتراک‌ها
    Route::get('subscriptions', [SuperSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions/{subscription}/cancel', [SuperSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/renew', [SuperSubscriptionController::class, 'renew'])->name('subscriptions.renew');

    Route::get('/subscriptions', fn() => view('super-admin.placeholder'))->name('subscriptions.index');
    Route::get('/payments', fn() => view('super-admin.placeholder'))->name('payments.index');
    Route::get('/licenses', fn() => view('super-admin.placeholder'))->name('licenses.index');
    Route::get('activity-logs', [SuperActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::resource('roles', SuperAdminRoleController::class)->except('show');
    Route::get('/tickets', fn() => view('super-admin.placeholder'))->name('tickets.index');
    Route::get('/notifications', fn() => view('super-admin.placeholder'))->name('notifications.index');
    Route::get('/settings', fn() => view('super-admin.placeholder'))->name('settings.index');
    Route::post('/tools/sync-permissions', [ToolController::class, 'syncPermissions'])->name('tools.sync-permissions');
    Route::post('/tools/clear-cache', [ToolController::class, 'clearCache'])->name('tools.clear-cache');
});

//Authentication-----------------
Route::middleware(['guest', 'throttle:6,1'])->group(function () {

    //Login-----------------
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    // صفحه وارد کردن موبایل برای دریافت OTP
    Route::get('/login/otp/request', [LoginController::class, 'showOtpRequestForm'])->name('login.otp.request');
    // ارسال کد OTP
    Route::post('/login/otp/send', [LoginController::class, 'sendOtp'])->name('login.otp.send');
    // صفحه وارد کردن کد تأیید
    Route::get('/login/otp/verify', [LoginController::class, 'showOtpVerifyForm'])->name('login.otp.verify');
    // تأیید کد
    Route::post('/login/otp/verify', [LoginController::class, 'verifyOtp'])->name('login.otp.verify.post');
    // ارسال مجدد کد
    Route::post('/login/otp/resend', [LoginController::class, 'resendOtp'])->name('login.otp.resend');

    //Register-----------------
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    // ارسال کد OTP - ثبت نام
    Route::get('/register/otp/request', [RegisterController::class, 'showOtpRequestForm'])->name('register.otp.form');
    Route::post('/register/otp/send', [RegisterController::class, 'sendOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/register/otp/resend', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');

    //Forget Password-----------------
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.send');
    Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
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

    // مدیریت کاربران
    Route::resource('users', UserController::class);
    Route::post('users/import', [UserController::class, 'import'])->name('users.import');
    Route::get('users/export', [UserController::class, 'export'])->name('users.export');

    // پروفایل
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');

    //سطوح دسترسی
    Route::resource('roles', RoleController::class)->except(['show', 'create', 'edit']);

    //سال مالی
    Route::resource('fiscal-years', FiscalYearController::class)->except('show');
    Route::post('fiscal-years/{fiscal_year}/activate', [FiscalYearController::class, 'activate'])->name('fiscal-years.activate');
    Route::post('fiscal-years/{fiscal_year}/close', [FiscalYearController::class, 'close'])->name('fiscal-years.close');
});


//مسیرهایی که مالک فقط باید دسترسی داشته باشد
Route::middleware(['auth', 'require.tenant', 'owner', 'check.subscription'])->group(function () {
    //مدیریت اشتراک ها
    Route::get('/billing/plans', [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/license', [BillingController::class, 'license'])->name('billing.license');
    Route::get('/billing/history', [BillingController::class, 'history'])->name('billing.history');

    //لاگ های سیستمی
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

    //مدیریت سازمان
    Route::resource('companies', CompanyController::class)->except('show');
});


Route::get('/test-owner', function () {
    $user = auth()->user();
    dd([
        'isOwner'        => $user->isOwner(),
        'tenant_id'      => $user->tenant_id,
        'root_company'   => $user->tenant?->rootCompany?->id,
        'root_company_name' => $user->tenant?->rootCompany?->name,
        'company_user_id'=> $user->companyUsers()->where('company_id', $user->tenant?->rootCompany?->id)->first()?->id,
        'role'           => $user->companyUsers()->where('company_id', $user->tenant?->rootCompany?->id)->first()?->roles()->first()?->title,
    ]);
});