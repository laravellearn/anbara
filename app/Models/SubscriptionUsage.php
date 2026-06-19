<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
    use Auditable,LogsActivity;
    protected $fillable = [
        'subscription_id',
        'feature_key',
        'used_value',
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}