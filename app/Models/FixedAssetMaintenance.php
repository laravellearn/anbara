<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetMaintenance extends Model
{
    protected $fillable = [
        'tenant_id', 'fixed_asset_id', 'maintenance_date', 'type',
        'description', 'cost', 'performed_by', 'next_maintenance_date', 'created_by',
    ];

    protected $casts = [
        'maintenance_date'      => 'date',
        'next_maintenance_date' => 'date',
        'cost'                  => 'decimal:2',
    ];

    const TYPES = [
        'repair'      => 'تعمیر',
        'service'     => 'سرویس دوره‌ای',
        'inspection'  => 'بازرسی',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }
}
