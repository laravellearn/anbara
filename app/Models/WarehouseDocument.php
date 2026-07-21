<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id',
        'document_number', 'type', 'status',
        'warehouse_id', 'destination_warehouse_id', 'warehouse_location_id',
        'contact_id', 'fiscal_year_id', 'cost_center_id',
        'document_date', 'reference_number', 'description',
        'created_by', 'approved_by', 'approved_at', 'rejected_at', 'rejection_reason',
    ];

    protected $casts = [
        'document_date' => 'date',
        'approved_at'   => 'datetime',
        'rejected_at'   => 'datetime',
    ];

    // ─── نوع سند ─────────────────────────────────────────────────────────────
    const TYPE_RECEIPT     = 'receipt';
    const TYPE_ISSUE       = 'issue';
    const TYPE_TRANSFER    = 'transfer';
    const TYPE_ADJUSTMENT  = 'adjustment';
    const TYPE_RETURN_IN   = 'return_in';
    const TYPE_RETURN_OUT  = 'return_out';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_RECEIPT    => 'رسید انبار',
            self::TYPE_ISSUE      => 'حواله انبار',
            self::TYPE_TRANSFER   => 'انتقال کالا',
            self::TYPE_ADJUSTMENT => 'تعدیل موجودی',
            self::TYPE_RETURN_IN  => 'مرجوعی ورودی',
            self::TYPE_RETURN_OUT => 'مرجوعی خروجی',
        ];
    }

    public static function typeColors(): array
    {
        return [
            self::TYPE_RECEIPT    => 'success',
            self::TYPE_ISSUE      => 'danger',
            self::TYPE_TRANSFER   => 'info',
            self::TYPE_ADJUSTMENT => 'warning',
            self::TYPE_RETURN_IN  => 'primary',
            self::TYPE_RETURN_OUT => 'secondary',
        ];
    }

    /** پیشوند شماره سند بر اساس نوع */
    public static function typePrefix(string $type): string
    {
        return match ($type) {
            self::TYPE_RECEIPT    => 'REC',
            self::TYPE_ISSUE      => 'ISS',
            self::TYPE_TRANSFER   => 'TRN',
            self::TYPE_ADJUSTMENT => 'ADJ',
            self::TYPE_RETURN_IN  => 'RIN',
            self::TYPE_RETURN_OUT => 'ROT',
            default               => 'DOC',
        };
    }

    // ─── وضعیت ───────────────────────────────────────────────────────────────
    const STATUS_DRAFT     = 'draft';
    const STATUS_PENDING   = 'pending';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT     => 'پیش‌نویس',
            self::STATUS_PENDING   => 'در انتظار تأیید',
            self::STATUS_APPROVED  => 'تأیید شده',
            self::STATUS_REJECTED  => 'رد شده',
            self::STATUS_CANCELLED => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT     => 'secondary',
            self::STATUS_PENDING   => 'warning',
            self::STATUS_APPROVED  => 'success',
            self::STATUS_REJECTED  => 'danger',
            self::STATUS_CANCELLED => 'dark',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────
    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function getTypeColorAttribute(): string
    {
        return self::typeColors()[$this->type] ?? 'secondary';
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'secondary';
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /** آیا این نوع سند باعث ورود کالا به انبار می‌شود؟ */
    public function isInbound(): bool
    {
        return in_array($this->type, [self::TYPE_RECEIPT, self::TYPE_RETURN_IN]);
    }

    /** آیا این نوع سند باعث خروج کالا از انبار می‌شود؟ */
    public function isOutbound(): bool
    {
        return in_array($this->type, [self::TYPE_ISSUE, self::TYPE_RETURN_OUT]);
    }

    // ─── Relations ────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(WarehouseDocumentItem::class)->orderBy('sort_order');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function destinationWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_warehouse_id');
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(WarehouseLocation::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────
    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function scopeOfType($q, string $type)
    {
        return $q->where('type', $type);
    }

    public function scopeWithStatus($q, string $status)
    {
        return $q->where('status', $status);
    }
}
