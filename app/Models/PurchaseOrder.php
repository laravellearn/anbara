<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'po_number', 'status',
        'supplier_id', 'warehouse_id', 'fiscal_year_id', 'cost_center_id',
        'order_date', 'expected_delivery_date', 'actual_delivery_date',
        'currency', 'discount_percent', 'tax_percent', 'shipping_cost',
        'reference_number', 'terms_and_conditions', 'notes',
        'created_by', 'confirmed_by', 'closed_by',
        'confirmed_at', 'sent_at', 'closed_at', 'cancellation_reason',
    ];

    protected $casts = [
        'order_date'              => 'date',
        'expected_delivery_date'  => 'date',
        'actual_delivery_date'    => 'date',
        'confirmed_at'            => 'datetime',
        'sent_at'                 => 'datetime',
        'closed_at'               => 'datetime',
        'discount_percent'        => 'decimal:2',
        'tax_percent'             => 'decimal:2',
        'shipping_cost'           => 'decimal:4',
    ];

    // ─── وضعیت‌ها ─────────────────────────────────────────────────────────────
    const STATUS_DRAFT            = 'draft';
    const STATUS_CONFIRMED        = 'confirmed';
    const STATUS_SENT             = 'sent';
    const STATUS_PARTIAL_RECEIVED = 'partial_received';
    const STATUS_RECEIVED         = 'received';
    const STATUS_CLOSED           = 'closed';
    const STATUS_CANCELLED        = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT            => 'پیش‌نویس',
            self::STATUS_CONFIRMED        => 'تأیید شده',
            self::STATUS_SENT             => 'ارسال شده به تأمین‌کننده',
            self::STATUS_PARTIAL_RECEIVED => 'دریافت جزئی',
            self::STATUS_RECEIVED         => 'دریافت کامل',
            self::STATUS_CLOSED           => 'بسته شده',
            self::STATUS_CANCELLED        => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT            => 'secondary',
            self::STATUS_CONFIRMED        => 'primary',
            self::STATUS_SENT             => 'info',
            self::STATUS_PARTIAL_RECEIVED => 'warning',
            self::STATUS_RECEIVED         => 'success',
            self::STATUS_CLOSED           => 'dark',
            self::STATUS_CANCELLED        => 'danger',
        ];
    }

    // ─── Accessors ────────────────────────────────────────────────────────────
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

    public function canConfirm(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canSend(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function canReceive(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_PARTIAL_RECEIVED, self::STATUS_CONFIRMED]);
    }

    public function canClose(): bool
    {
        return in_array($this->status, [self::STATUS_RECEIVED, self::STATUS_PARTIAL_RECEIVED]);
    }

    public function canCancel(): bool
    {
        return !in_array($this->status, [self::STATUS_CLOSED, self::STATUS_CANCELLED, self::STATUS_RECEIVED]);
    }

    /** جمع کل سفارش قبل از تخفیف و مالیات */
    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($i) =>
            (float)$i->quantity_ordered * (float)($i->unit_price ?? 0) * (1 - (float)$i->discount_percent / 100)
        );
    }

    /** مبلغ تخفیف کلی */
    public function getDiscountAmountAttribute(): float
    {
        return $this->subtotal * (float)$this->discount_percent / 100;
    }

    /** مبلغ مالیات */
    public function getTaxAmountAttribute(): float
    {
        return ($this->subtotal - $this->discount_amount) * (float)$this->tax_percent / 100;
    }

    /** مبلغ کل نهایی */
    public function getTotalAmountAttribute(): float
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount + (float)$this->shipping_cost;
    }

    /** درصد دریافت */
    public function getReceiptPercentAttribute(): float
    {
        $ordered  = $this->items->sum('quantity_ordered');
        $received = $this->items->sum('quantity_received');
        return $ordered > 0 ? round($received / $ordered * 100, 1) : 0;
    }

    // ─── Relations ────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class)->orderBy('sort_order');
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────
    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function scopeWithStatus($q, string $status)
    {
        return $q->where('status', $status);
    }
}
