<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesInvoice extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany;

    protected $fillable = [
        'tenant_id', 'company_id', 'invoice_number', 'status',
        'invoice_date', 'due_date', 'customer_id', 'warehouse_id',
        'warehouse_document_id', 'fiscal_year_id', 'cost_center_id',
        'subtotal', 'discount_percent', 'discount_amount',
        'tax_percent', 'tax_amount', 'total_amount', 'paid_amount',
        'description', 'reference_number', 'created_by', 'confirmed_by', 'confirmed_at',
    ];

    protected $casts = [
        'invoice_date'  => 'date',
        'due_date'      => 'date',
        'confirmed_at'  => 'datetime',
        'subtotal'      => 'float',
        'discount_amount' => 'float',
        'tax_amount'    => 'float',
        'total_amount'  => 'float',
        'paid_amount'   => 'float',
    ];

    const STATUS_DRAFT           = 'draft';
    const STATUS_CONFIRMED       = 'confirmed';
    const STATUS_PARTIALLY_PAID  = 'partially_paid';
    const STATUS_PAID            = 'paid';
    const STATUS_CANCELLED       = 'cancelled';

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT          => 'پیش‌نویس',
            self::STATUS_CONFIRMED      => 'تأیید شده',
            self::STATUS_PARTIALLY_PAID => 'پرداخت جزئی',
            self::STATUS_PAID           => 'تسویه شده',
            self::STATUS_CANCELLED      => 'لغو شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT          => 'secondary',
            self::STATUS_CONFIRMED      => 'primary',
            self::STATUS_PARTIALLY_PAID => 'warning',
            self::STATUS_PAID           => 'success',
            self::STATUS_CANCELLED      => 'danger',
        ];
    }

    // ─── روابط ────────────────────────────────────────────────────────────────
    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Contact::class, 'customer_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function warehouseDocument()
    {
        return $this->belongsTo(WarehouseDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ─── scopes ───────────────────────────────────────────────────────────────
    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    // ─── helpers ──────────────────────────────────────────────────────────────
    public function remainingAmount(): float
    {
        return max(0, $this->total_amount - $this->paid_amount);
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canConfirm(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function canCancel(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_CONFIRMED]);
    }
}
