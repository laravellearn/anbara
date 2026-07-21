<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckLowStockJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private ?int $tenantId   = null,  // null = همه سازمان‌ها
        private ?int $companyId  = null,
    ) {}

    public function handle(NotificationService $notif): void
    {
        Log::info('CheckLowStockJob: شروع بررسی موجودی زیر حداقل');

        $query = Product::with(['inventories', 'tenant'])
            ->whereNotNull('min_stock')
            ->where('min_stock', '>', 0)
            ->where('is_active', true);

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
            if ($this->companyId) $query->where('company_id', $this->companyId);
        }

        $products = $query->get();
        $count    = 0;

        foreach ($products as $product) {
            // محاسبه موجودی کل این محصول در این سازمان
            $currentStock = $product->inventories
                ->where('tenant_id', $product->tenant_id)
                ->sum('quantity');

            if ($currentStock <= $product->min_stock) {
                // یافتن مدیران این tenant
                $adminUserIds = \App\Models\User::where('tenant_id', $product->tenant_id)
                    ->whereHas('roles', fn($q) => $q->whereIn('slug', ['admin', 'warehouse-manager', 'owner']))
                    ->pluck('id');

                foreach ($adminUserIds as $uid) {
                    $notif->send(
                        userId:    $uid,
                        type:      'low_stock',
                        title:     'موجودی کم: ' . $product->title,
                        body:      'موجودی فعلی ' . number_format($currentStock) . ' — حداقل ' . number_format($product->min_stock),
                        icon:      'alert-triangle',
                        color:     'warning',
                        actionUrl: '/warehouse/products/' . $product->id,
                    );
                }
                $count++;
            }
        }

        Log::info("CheckLowStockJob: {$count} کالای زیر حداقل موجودی یافت شد.");
    }
}
