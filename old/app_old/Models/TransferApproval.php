<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferApproval extends Model
{
    protected $fillable = [

        'transfer_id',

        'user_id',

        'level',

        'status',

        'comment',

        'action_at'
    ];

    protected $casts = [
        'action_at' => 'datetime',
        'status' => TransferApprovalStatus::class,

    ];

    public function transfer()
    {
        return $this->belongsTo(
            Transfer::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }
}