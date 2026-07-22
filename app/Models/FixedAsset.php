<?php

namespace App\Models;

use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAsset extends Model
{
    use SoftDeletes, LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'asset_code', 'title', 'serial_number',
        'description', 'category', 'location', 'purchase_price', 'current_value',
        'purchase_date', 'warranty_expiry', 'status', 'image', 'created_by',
    ];

    protected $casts = [
        'purchase_date'   => 'date',
        'warranty_expiry' => 'date',
        'purchase_price'  => 'decimal:2',
        'current_value'   => 'decimal:2',
    ];

    // وضعیت‌های دارایی
    const STATUSES = [
        'active'            => 'فعال',
        'assigned'          => 'تخصیص‌یافته',
        'under_maintenance' => 'در تعمیر',
        'retired'           => 'بازنشسته',
        'scrapped'          => 'اسقاط',
    ];

    const CATEGORIES = [
        'building'    => 'ساختمان و تأسیسات',
        'vehicle'     => 'خودرو و وسایل نقلیه',
        'equipment'   => 'تجهیزات و ماشین‌آلات',
        'furniture'   => 'مبلمان و لوازم اداری',
        'it'          => 'تجهیزات فناوری اطلاعات',
        'other'       => 'سایر',
    ];

    // ─── روابط ───────────────────────────────────────────────────────────────
    public function assignments(): HasMany
    {
        return $this->hasMany(FixedAssetAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(FixedAssetAssignment::class)->where('status', 'active')->latest();
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(FixedAssetMaintenance::class)->latest('maintenance_date');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────
    public function scopeForTenant($query, int $tenantId, int $companyId)
    {
        return $query->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }
}
