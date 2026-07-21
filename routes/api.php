<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\InventoryApiController;
use App\Http\Controllers\Api\DocumentApiController;

/*
|--------------------------------------------------------------------------
| API Routes — Anbara RESTful API (Laravel Sanctum)
|--------------------------------------------------------------------------
|
| پیش‌نیاز: SANCTUM_STATEFUL_DOMAINS در .env تنظیم شود
| احراز هویت: Bearer Token (Sanctum)
|
*/

// ─── احراز هویت (بدون token) ──────────────────────────────────────────────────
Route::prefix('v1')->group(function () {
    Route::post('/auth/token',  [AuthApiController::class, 'token'])->name('api.auth.token');
});

// ─── مسیرهای محافظت‌شده با Sanctum token ─────────────────────────────────────
Route::prefix('v1')->middleware(['auth:sanctum', 'require.tenant'])->group(function () {

    // اطلاعات کاربر جاری
    Route::get('/auth/me',     [AuthApiController::class, 'me'])->name('api.auth.me');
    Route::post('/auth/logout',[AuthApiController::class, 'logout'])->name('api.auth.logout');

    // ─── کالاها ────────────────────────────────────────────────────────────────
    Route::get('/products',        [ProductApiController::class, 'index'])->name('api.products.index');
    Route::get('/products/{id}',   [ProductApiController::class, 'show'])->name('api.products.show');

    // ─── موجودی انبار ─────────────────────────────────────────────────────────
    Route::get('/inventory',                    [InventoryApiController::class, 'index'])->name('api.inventory.index');
    Route::get('/inventory/below-minimum',      [InventoryApiController::class, 'belowMinimum'])->name('api.inventory.below-minimum');
    Route::get('/inventory/products/{id}',      [InventoryApiController::class, 'product'])->name('api.inventory.product');

    // ─── اسناد انبار ──────────────────────────────────────────────────────────
    Route::get('/documents',       [DocumentApiController::class, 'index'])->name('api.documents.index');
    Route::get('/documents/{id}',  [DocumentApiController::class, 'show'])->name('api.documents.show');
});
