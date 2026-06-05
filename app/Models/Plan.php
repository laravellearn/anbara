<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'monthly_price',
        'yearly_price',
        'currency',
        'duration_days',
        'limits',
        'features',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'limits'   => 'array',    // خودکار json_decode شود
        'features' => 'array',
        'is_active'=> 'boolean',
        'price'    => 'decimal:2',
    ];

    /**
     * چک می‌کنه که یک feature خاص تو این پلن فعال هست یا نه.
     */
    public function hasFeature(string $featureKey): bool
    {
        return in_array($featureKey, $this->features ?? []);
    }

    /**
     * مقدار محدودیت یک ویژگی را برمی‌گرداند.
     */
    public function getLimit(string $limitKey): ?int
    {
        return $this->limits[$limitKey] ?? null;
    }

    // رابطه‌ها
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}