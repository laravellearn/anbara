<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_OPEN         = 'open';
    const STATUS_IN_PROGRESS  = 'in_progress';
    const STATUS_WAITING_USER = 'waiting_user';
    const STATUS_RESOLVED     = 'resolved';
    const STATUS_CLOSED       = 'closed';

    protected $fillable = [
        'tenant_id', 'user_id', 'assigned_to',
        'status', 'priority', 'category',
        'subject', 'description',
        'resolved_at', 'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
    ];

    // ─── Auto ticket_number ────────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $t) {
            $t->ticket_number = 'TKT-' . strtoupper(substr(uniqid(), -6));
        });
    }

    // ─── Relations ────────────────────────────────────────────────────────
    public function user()         { return $this->belongsTo(User::class); }
    public function assignedUser() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function tenant()       { return $this->belongsTo(Tenant::class); }
    public function replies()      { return $this->hasMany(TicketReply::class)->orderBy('created_at'); }

    // ─── Helpers ──────────────────────────────────────────────────────────
    public function canReply(): bool
    {
        return !in_array($this->status, [self::STATUS_CLOSED]);
    }

    // ─── Static labels / colors ───────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            self::STATUS_OPEN         => 'باز',
            self::STATUS_IN_PROGRESS  => 'در جریان',
            self::STATUS_WAITING_USER => 'در انتظار کاربر',
            self::STATUS_RESOLVED     => 'حل شده',
            self::STATUS_CLOSED       => 'بسته',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_OPEN         => 'danger',
            self::STATUS_IN_PROGRESS  => 'warning',
            self::STATUS_WAITING_USER => 'info',
            self::STATUS_RESOLVED     => 'success',
            self::STATUS_CLOSED       => 'secondary',
        ];
    }

    public static function priorityLabels(): array
    {
        return ['low' => 'پایین', 'normal' => 'متوسط', 'high' => 'بالا', 'urgent' => 'فوری'];
    }

    public static function priorityColors(): array
    {
        return ['low' => 'secondary', 'normal' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
    }

    public static function categoryLabels(): array
    {
        return [
            'general'   => 'عمومی',
            'billing'   => 'مالی / اشتراک',
            'technical' => 'فنی',
            'warehouse' => 'انبارداری',
        ];
    }
}
