<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'monthly_price',
        'yearly_price',
        'description',
        'is_active',
    ];

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}