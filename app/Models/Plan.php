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

    // app/Models/Plan.php
    protected $casts = [
        'monthly_price' => 'float',
        'yearly_price'  => 'float',
        'limits'        => 'array',
        'features'      => 'array',
        'is_active'     => 'boolean',
        'duration_days' => 'integer',
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
