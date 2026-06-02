<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PaymentStatus;

class Payment extends Model
{
    protected $fillable = [
        'tenant_id',
        'subscription_id',

        'gateway',
        'transaction_id',
        'ref_id',

        'amount',

        'status',

        'description',
    ];

    protected $casts = [
        'amount' => 'decimal:0',
        'status' => PaymentStatus::class,
    ];

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}