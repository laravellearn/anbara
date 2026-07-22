<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $table = 'webhook_logs';

    protected $fillable = [
        'direction',   // incoming | outgoing
        'type',        // hello | sepidaar | rafeh | generic
        'url',
        'headers',
        'payload',
        'response',
        'ip_address',
        'status',      // received | processed | failed | sent | error
        'tenant_id',
    ];

    protected $casts = [
        'headers'  => 'array',
        'payload'  => 'array',
        'response' => 'array',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
