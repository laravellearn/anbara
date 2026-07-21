<?php

namespace App\Console\Commands;

use App\Jobs\CheckLowStockJob;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    protected $signature   = 'stock:check-low {--tenant= : محدود کردن به یک سازمان خاص (tenant_id)}';
    protected $description = 'بررسی کالاهای زیر حداقل موجودی و ارسال اعلان';

    public function handle(): int
    {
        $tenantId = $this->option('tenant') ? (int)$this->option('tenant') : null;
        CheckLowStockJob::dispatch($tenantId);
        $this->info('Job بررسی موجودی در صف قرار گرفت.');
        return self::SUCCESS;
    }
}
