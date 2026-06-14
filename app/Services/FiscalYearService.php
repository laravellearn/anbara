<?php
// app/Services/FiscalYearService.php
namespace App\Services;

use App\Models\FiscalYear;
use App\Models\Tenant;
use Carbon\Carbon;

class FiscalYearService
{
    protected ?Tenant $tenant;

    public function __construct(?Tenant $tenant = null)
    {
        $this->tenant = $tenant ?? (auth()->check() ? auth()->user()->tenant : null);
    }

    /**
     * سال مالی‌ای که تاریخ امروز در بازه آن باشد (و is_active = true)
     * اگر نبود، آخرین سال فعال (یا اولین) را برمی‌گرداند.
     */
    public function current(): ?FiscalYear
    {
        if (!$this->tenant) return null;

        $today = Carbon::today(); // یا verta برای شمسی

        // ابتدا سعی بر اساس تاریخ امروز و فعال
        $active = $this->tenant->fiscalYears()
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->first();

        if ($active) return $active;

        // در غیر این صورت، سالی که is_active=true باشد (حتی اگر امروز در آن نباشد)
        $active = $this->tenant->fiscalYears()
            ->where('is_active', true)
            ->first();

        return $active;
    }

    /**
     * آیا سال مالی برای ویرایش/ثبت سند باز است؟
     */
    public function isOpen(?FiscalYear $year = null): bool
    {
        $year = $year ?? $this->current();
        return $year && !$year->is_closed;
    }
}
