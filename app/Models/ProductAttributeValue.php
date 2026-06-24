<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttributeValue extends Model
{
    use SoftDeletes, BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany,Auditable,LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'product_id', 'attribute_id', 'value',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}