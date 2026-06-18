<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, Auditable;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'type',
        'options',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class, 'attribute_id');
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_type_attribute')
            ->withPivot('is_required', 'sort_order');
    }
}
