<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class InvoicePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id',
        'sales_invoice_id', 'purchase_invoice_id',
        'fiscal_year_id',
        'payment_date', 'amount', 'payment_method',
        'reference_number', 'cheque_date', 'bank_name', 'account_number',
        'notes', 'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'cheque_date'  => 'date',
        'amount'       => 'decimal:4',
    ];

    public function scopeForTenant(Builder $q, int $tenantId, ?int $companyId = null): Builder
    {
        $q->where('tenant_id', $tenantId);
        if ($companyId) $q->where('company_id', $companyId);
        return $q;
    }

    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cash'          => 'نقدی',
            'cheque'        => 'چک',
            'bank_transfer' => 'انتقال بانکی',
            'card'          => 'کارت به کارت',
            default         => $this->payment_method,
        };
    }
}
