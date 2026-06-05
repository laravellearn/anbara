<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionUsage extends Model
{
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