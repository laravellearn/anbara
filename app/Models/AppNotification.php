<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppNotification extends Model
{
    protected $fillable = [
        'tenant_id', 'user_id', 'type', 'title', 'body',
        'icon', 'color', 'action_url', 'is_read', 'read_at',
        'notifiable_type', 'notifiable_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ─── روابط ────────────────────────────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    // ─── scopes ───────────────────────────────────────────────────────────────
    public function scopeUnread($q)
    {
        return $q->where('is_read', false);
    }

    public function scopeForUser($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    // ─── انواع اعلان ─────────────────────────────────────────────────────────
    const TYPE_LOW_STOCK            = 'low_stock';
    const TYPE_DOC_APPROVED         = 'doc_approved';
    const TYPE_DOC_REJECTED         = 'doc_rejected';
    const TYPE_DOC_SUBMITTED        = 'doc_submitted';
    const TYPE_PO_RECEIVED          = 'po_received';
    const TYPE_SUBSCRIPTION_EXPIRING = 'subscription_expiring';
    const TYPE_PAYMENT_SUCCESS      = 'payment_success';
    const TYPE_ITEM_REQUEST         = 'item_request';

    public static function typeConfig(): array
    {
        return [
            self::TYPE_LOW_STOCK             => ['icon' => 'alert-triangle', 'color' => 'warning'],
            self::TYPE_DOC_APPROVED          => ['icon' => 'check-circle',   'color' => 'success'],
            self::TYPE_DOC_REJECTED          => ['icon' => 'x-circle',       'color' => 'danger'],
            self::TYPE_DOC_SUBMITTED         => ['icon' => 'file-text',      'color' => 'info'],
            self::TYPE_PO_RECEIVED           => ['icon' => 'package',        'color' => 'success'],
            self::TYPE_SUBSCRIPTION_EXPIRING => ['icon' => 'clock',          'color' => 'warning'],
            self::TYPE_PAYMENT_SUCCESS       => ['icon' => 'credit-card',    'color' => 'success'],
            self::TYPE_ITEM_REQUEST          => ['icon' => 'shopping-cart',  'color' => 'primary'],
        ];
    }
}
