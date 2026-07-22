<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PriceHistory extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id',
        'product_id', 'supplier_id',
        'unit_price', 'currency',
        'price_date', 'source', 'notes',
        'recorded_by',
    ];

    protected $casts = [
        'price_date' => 'date',
        'unit_price' => 'decimal:4',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Contact::class, 'supplier_id');
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getSourceLabelAttribute(): string
    {
        return match($this->source) {
            'manual'         => 'دستی',
            'purchase_order' => 'سفارش خرید',
            'invoice'        => 'فاکتور',
            default          => $this->source,
        };
    }
}
