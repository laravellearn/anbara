<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ReturnInvoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id', 'return_number', 'type', 'status',
        'sales_invoice_id', 'purchase_invoice_id',
        'fiscal_year_id', 'cost_center_id', 'warehouse_id', 'contact_id',
        'return_date', 'reason',
        'subtotal', 'discount_amount', 'tax_amount', 'total_amount',
        'notes', 'reference_number',
        'created_by', 'confirmed_by', 'confirmed_at',
    ];

    protected $casts = [
        'return_date'   => 'date',
        'confirmed_at'  => 'datetime',
        'subtotal'      => 'decimal:4',
        'discount_amount' => 'decimal:4',
        'tax_amount'    => 'decimal:4',
        'total_amount'  => 'decimal:4',
    ];

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForTenant(Builder $q, int $tenantId, ?int $companyId = null): Builder
    {
        $q->where('tenant_id', $tenantId);
        if ($companyId) {
            $q->where('company_id', $companyId);
        }
        return $q;
    }

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    // ─── Relations ────────────────────────────────────────────────────────────

    public function items()
    {
        return $this->hasMany(ReturnInvoiceItem::class);
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'sales' ? 'برگشت از فروش' : 'برگشت از خرید';
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'پیش‌نویس',
            'confirmed' => 'تأیید شده',
            'cancelled' => 'لغو شده',
            default     => $this->status,
        };
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }
}
