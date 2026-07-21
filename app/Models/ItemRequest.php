<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemRequest extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'ir_number', 'status',
        'requester_id', 'approver_id', 'warehouse_id',
        'organizational_unit_id', 'fiscal_year_id', 'cost_center_id',
        'warehouse_document_id', 'request_date', 'required_by_date',
        'priority', 'purpose', 'notes', 'rejection_reason',
        'submitted_at', 'approved_at', 'rejected_at', 'issued_at',
        'created_by',
    ];

    protected $casts = [
        'request_date'     => 'date',
        'required_by_date' => 'date',
        'submitted_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'issued_at'        => 'datetime',
    ];

    const STATUS_DRAFT     = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED  = 'approved';
    const STATUS_ISSUED    = 'issued';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT     => 'پیش‌نویس',
            self::STATUS_SUBMITTED => 'ارسال شده',
            self::STATUS_APPROVED  => 'تأیید شده',
            self::STATUS_ISSUED    => 'صدور حواله',
            self::STATUS_REJECTED  => 'رد شده',
            self::STATUS_CANCELLED => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT     => 'secondary',
            self::STATUS_SUBMITTED => 'info',
            self::STATUS_APPROVED  => 'success',
            self::STATUS_ISSUED    => 'primary',
            self::STATUS_REJECTED  => 'danger',
            self::STATUS_CANCELLED => 'warning',
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

    public function isEditable(): bool { return $this->status === self::STATUS_DRAFT; }
    public function canSubmit(): bool  { return $this->status === self::STATUS_DRAFT; }
    public function canApprove(): bool { return $this->status === self::STATUS_SUBMITTED; }
    public function canReject(): bool  { return $this->status === self::STATUS_SUBMITTED; }
    public function canIssue(): bool   { return $this->status === self::STATUS_APPROVED; }
    public function canCancel(): bool  { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SUBMITTED]); }

    // ─── Relations ────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(ItemRequestItem::class)->orderBy('sort_order');
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

    public function organizationalUnit()
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function warehouseDocument()
    {
        return $this->belongsTo(WarehouseDocument::class);
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
