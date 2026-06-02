<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\{
    LoginController,


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


});

// خروج
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
});



