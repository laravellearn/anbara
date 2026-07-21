<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'invoice_number', 'supplier_invoice_number',
        'status', 'supplier_id', 'purchase_order_id', 'fiscal_year_id', 'cost_center_id',
        'invoice_date', 'due_date', 'currency',
        'discount_percent', 'tax_percent', 'shipping_cost',
        'payment_method', 'payment_reference', 'payment_date',
        'notes', 'cancellation_reason',
        'created_by', 'registered_by', 'registered_at',
    ];

    protected $casts = [
        'invoice_date'    => 'date',
        'due_date'        => 'date',
        'payment_date'    => 'date',
        'registered_at'   => 'datetime',
        'discount_percent'=> 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'shipping_cost'   => 'decimal:4',
    ];

    const STATUS_DRAFT      = 'draft';
    const STATUS_REGISTERED = 'registered';
    const STATUS_MATCHED    = 'matched';
    const STATUS_PAID       = 'paid';
    const STATUS_CANCELLED  = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT      => 'پیش‌نویس',
            self::STATUS_REGISTERED => 'ثبت شده',
            self::STATUS_MATCHED    => 'تطبیق با سفارش',
            self::STATUS_PAID       => 'پرداخت شده',
            self::STATUS_CANCELLED  => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT      => 'secondary',
            self::STATUS_REGISTERED => 'info',
            self::STATUS_MATCHED    => 'primary',
            self::STATUS_PAID       => 'success',
            self::STATUS_CANCELLED  => 'danger',
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
    public function canRegister(): bool { return $this->status === self::STATUS_DRAFT; }
    public function canMatch(): bool    { return in_array($this->status, [self::STATUS_REGISTERED]); }
    public function canPay(): bool      { return in_array($this->status, [self::STATUS_REGISTERED, self::STATUS_MATCHED]); }
    public function canCancel(): bool   { return !in_array($this->status, [self::STATUS_PAID, self::STATUS_CANCELLED]); }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn($i) =>
            (float)$i->quantity * (float)$i->unit_price * (1 - (float)$i->discount_percent / 100)
        );
    }

    public function getTaxAmountAttribute(): float
    {
        return $this->subtotal * (float)$this->tax_percent / 100;
    }

    public function getDiscountAmountAttribute(): float
    {
        return $this->subtotal * (float)$this->discount_percent / 100;
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->subtotal - $this->discount_amount + $this->tax_amount + (float)$this->shipping_cost;
    }

    // ─── Relations ────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class)->orderBy('sort_order');
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function invoicePayments()
    {
        return $this->hasMany(\App\Models\InvoicePayment::class, 'purchase_invoice_id');
    }

    public function returnInvoices()
    {
        return $this->hasMany(\App\Models\ReturnInvoice::class, 'purchase_invoice_id');
    }
}
