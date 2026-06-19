<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use Auditable, LogsActivity;
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

    /**
     * بررسی می‌کند که یک قابلیت خاص در این پلن وجود دارد یا خیر.
     */
    public function hasFeature(string $featureKey): bool
    {
        $features = $this->features ?? [];
        return in_array($featureKey, $features);
    }
}
