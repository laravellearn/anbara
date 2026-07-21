<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT     = 'draft';
    const STATUS_SENT      = 'sent';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_EXPIRED   = 'expired';
    const STATUS_CONVERTED = 'converted';

    protected $fillable = [
        'tenant_id', 'company_id', 'quotation_number', 'status',
        'customer_id', 'warehouse_id', 'fiscal_year_id', 'cost_center_id',
        'quotation_date', 'valid_until', 'reference_number', 'description', 'terms',
        'subtotal', 'discount_percent', 'discount_amount', 'tax_percent', 'tax_amount', 'total_amount',
        'sales_invoice_id', 'created_by',
    ];

    protected $casts = [
        'quotation_date' => 'date',
        'valid_until'    => 'date',
        'subtotal'       => 'float',
        'discount_amount'=> 'float',
        'tax_amount'     => 'float',
        'total_amount'   => 'float',
    ];

    // ─── Auto quotation_number ────────────────────────────────────────────
    protected static function booted(): void
    {
        static::creating(function (self $q) {
            if (!$q->quotation_number) {
                $last = self::where('tenant_id', $q->tenant_id)->lockForUpdate()->max('id') ?? 0;
                $q->quotation_number = 'QT-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ─── Scopes ──────────────────────────────────────────────────────────
    public function scopeForTenant($q, int $tenantId, ?int $companyId = null)
    {
        $q->where('tenant_id', $tenantId);
        if ($companyId) $q->where('company_id', $companyId);
        return $q;
    }

    // ─── Relations ───────────────────────────────────────────────────────
    public function customer()     { return $this->belongsTo(Contact::class, 'customer_id'); }
    public function warehouse()    { return $this->belongsTo(Warehouse::class); }
    public function fiscalYear()   { return $this->belongsTo(FiscalYear::class); }
    public function costCenter()   { return $this->belongsTo(CostCenter::class); }
    public function creator()      { return $this->belongsTo(User::class, 'created_by'); }
    public function salesInvoice() { return $this->belongsTo(SalesInvoice::class); }
    public function items()        { return $this->hasMany(QuotationItem::class)->orderBy('sort_order'); }

    // ─── Helpers ─────────────────────────────────────────────────────────
    public function isEditable(): bool { return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT]); }
    public function canConvert(): bool { return $this->status === self::STATUS_ACCEPTED; }

    // ─── Static labels / colors ──────────────────────────────────────────
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT     => 'پیش‌نویس',
            self::STATUS_SENT      => 'ارسال شده',
            self::STATUS_ACCEPTED  => 'پذیرفته شده',
            self::STATUS_REJECTED  => 'رد شده',
            self::STATUS_EXPIRED   => 'منقضی شده',
            self::STATUS_CONVERTED => 'تبدیل به فاکتور شده',
        ];
    }

    public static function statusColors(): array
    {
        return [
            self::STATUS_DRAFT     => 'secondary',
            self::STATUS_SENT      => 'info',
            self::STATUS_ACCEPTED  => 'success',
            self::STATUS_REJECTED  => 'danger',
            self::STATUS_EXPIRED   => 'warning',
            self::STATUS_CONVERTED => 'primary',
        ];
    }
}
