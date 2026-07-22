<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ReorderRule extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany;

    protected $fillable = [
        'tenant_id','company_id','product_id','warehouse_id',
        'preferred_supplier_id','reorder_point','reorder_quantity',
        'safety_stock','lead_time_days','is_active','last_suggested_at',
    ];

    protected $casts = [
        'reorder_point'    => 'decimal:4',
        'reorder_quantity' => 'decimal:4',
        'safety_stock'     => 'decimal:4',
        'is_active'        => 'boolean',
        'last_suggested_at'=> 'datetime',
    ];

    public function product()           { return $this->belongsTo(Product::class); }
    public function warehouse()         { return $this->belongsTo(Warehouse::class); }
    public function preferredSupplier() { return $this->belongsTo(Contact::class, 'preferred_supplier_id'); }

    public function scopeForTenant($q, int $tenantId, int $companyId)
    {
        return $q->where('tenant_id', $tenantId)->where('company_id', $companyId);
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
}
