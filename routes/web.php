<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController


};
use App\Http\Controllers\{
    DashboardController
};



//Authentication-----------------
Route::middleware('guest')->group(function () {
    // لاگین
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

    // ثبت نام
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    // ارسال کد OTP - ثبت نام
    Route::get('/register/otp/request', [RegisterController::class, 'showOtpRequestForm'])->name('register.otp.form');
    Route::post('/register/otp/send', [RegisterController::class, 'sendOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [RegisterController::class, 'verifyOtp'])->name('register.otp.verify');
    Route::post('/register/otp/resend', [RegisterController::class, 'resendOtp'])->name('register.otp.resend');


    Route::get('/forgot-password', [ForgotPasswordController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.send');
    Route::get('/forgot-password/otp', [ForgotPasswordController::class, 'showOtpForm'])->name('password.otp.form');
    Route::post('/forgot-password/otp', [ForgotPasswordController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('/forgot-password/otp/resend', [ForgotPasswordController::class, 'resendOtp'])->name('password.otp.resend');
    Route::get('/forgot-password/reset', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.reset');
});

// خروج
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});



