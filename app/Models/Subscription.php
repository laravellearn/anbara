<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'starts_at',
        'ends_at',
        'status',
        'auto_renew',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function usages()
    {
        return $this->hasMany(SubscriptionUsage::class);
    }
}