<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'pr_number', 'status',
        'requester_id', 'approver_id', 'warehouse_id',
        'fiscal_year_id', 'cost_center_id', 'purchase_order_id',
        'request_date', 'required_by_date', 'priority',
        'reference_number', 'reason', 'notes', 'rejection_reason',
        'submitted_at', 'approved_at', 'rejected_at', 'converted_at',
        'created_by',
    ];

    protected $casts = [
        'request_date'     => 'date',
        'required_by_date' => 'date',
        'submitted_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'converted_at'     => 'datetime',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED  = 'approved';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CONVERTED = 'converted';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_URGENT = 'urgent';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT     => 'پیش‌نویس',
            self::STATUS_SUBMITTED => 'ارسال شده',
            self::STATUS_APPROVED  => 'تأیید شده',
            self::STATUS_REJECTED  => 'رد شده',
            self::STATUS_CONVERTED => 'تبدیل به سفارش',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT     => 'secondary',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_APPROVED  => 'success',
            self::STATUS_REJECTED  => 'danger',
            self::STATUS_CONVERTED => 'primary',
        ];
    }

    public static function priorityLabels(): array
    {
        return [
            self::PRIORITY_LOW    => 'کم',
            self::PRIORITY_NORMAL => 'معمولی',
            self::PRIORITY_HIGH   => 'زیاد',
            self::PRIORITY_URGENT => 'فوری',
        ];
    }

    public static function priorityColors(): array
    {
        return [
            self::PRIORITY_LOW    => 'secondary',
            self::PRIORITY_NORMAL => 'primary',
            self::PRIORITY_HIGH   => 'warning',
            self::PRIORITY_URGENT => 'danger',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::statusColors()[$this->status] ?? 'secondary';
    }

    public function getPriorityLabelAttribute(): string
    {
        return self::priorityLabels()[$this->priority] ?? $this->priority;
    }

    public function getPriorityColorAttribute(): string
    {
        return self::priorityColors()[$this->priority] ?? 'secondary';
    }

    public function isEditable(): bool   { return $this->status === self::STATUS_DRAFT; }
    public function canSubmit(): bool    { return $this->status === self::STATUS_DRAFT; }
    public function canApprove(): bool   { return $this->status === self::STATUS_SUBMITTED; }
    public function canReject(): bool    { return $this->status === self::STATUS_SUBMITTED; }
    public function canConvert(): bool   { return $this->status === self::STATUS_APPROVED; }

    public function getTotalEstimatedAttribute(): float
    {
        return $this->items->sum(fn($i) =>
            (float)$i->quantity_requested * (float)($i->estimated_unit_price ?? 0)
        );
    }

    // ─── Relations ────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class)->orderBy('sort_order');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }
}
