<?php

use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Anbara
|--------------------------------------------------------------------------
| مسیرهای API برای اپلیکیشن موبایل PWA و یکپارچه‌سازی خارجی
*/

Route::middleware(['auth:sanctum'])->group(function () {

    // ─── بارکد / کالا ──────────────────────────────────────────────────────
    Route::prefix('barcode')->group(function () {
        Route::get('/{code}',           [BarcodeController::class, 'lookup'])->name('api.barcode.lookup');
        Route::get('/{code}/stock',     [BarcodeController::class, 'stock'])->name('api.barcode.stock');
    });

    // ─── وضعیت موجودی کالا ─────────────────────────────────────────────────
    Route::get('/inventory',            [\App\Http\Controllers\Api\InventoryApiController::class, 'index'])->name('api.inventory.index');

    // ─── اسناد انبار ────────────────────────────────────────────────────────
    Route::get('/documents',            [\App\Http\Controllers\Api\InventoryApiController::class, 'documents'])->name('api.documents.index');
});

// ─── Webhook ورودی (از سیستم‌های خارجی) ──────────────────────────────────
Route::post('/webhooks/incoming/{type}', [WebhookController::class, 'receive'])
    ->name('api.webhooks.incoming')
    ->middleware('throttle:60,1');

// ─── QR Code اصالت‌سنجی (بدون auth — عمومی) ──────────────────────────────
Route::get('/verify/{uuid}', [\App\Http\Controllers\Api\DocumentVerifyController::class, 'show'])
    ->name('api.verify.document');
