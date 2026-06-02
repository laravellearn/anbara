<?php

namespace App\Models;

use ActivityAction;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [

        'tenant_id',
        'organization_id',

        'user_id',

        'action',

        'model_type',
        'model_id',

        'ip_address',

        'user_agent',

        'old_values',

        'new_values',

        'description',
    ];

    protected $casts = [

        'old_values' => 'array',

        'new_values' => 'array',
        'action' => ActivityAction::class,

    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function subject()
    {
        return $this->morphTo(
            'model'
        );
    }
}