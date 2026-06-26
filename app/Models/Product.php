<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes, Auditable, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'category_id',
        'brand_id',
        'product_type_id',
        'measurement_unit_id',
        'sku',
        'barcode',
        'title',
        'model',
        'part_number',
        'description',
        'minimum_stock',
        'maximum_stock',
        'is_asset',
        'is_active',
    ];

    protected $casts = [
        'minimum_stock' => 'decimal:4',
        'maximum_stock' => 'decimal:4',
        'is_asset' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function baseMeasurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_unit_id');
    }

    public function measurementUnits()
    {
        return $this->belongsToMany(MeasurementUnit::class, 'product_measurement_units')
            ->withPivot('conversion_factor', 'is_default', 'company_id')
            ->withTimestamps();
    }

    public function attributeValues()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function alternatives()
    {
        return $this->belongsToMany(Product::class, 'product_alternatives', 'product_id', 'alternative_product_id')
            ->withPivot('company_id')
            ->withTimestamps();
    }

    public function alternativeOf()
    {
        return $this->belongsToMany(Product::class, 'product_alternatives', 'alternative_product_id', 'product_id')
            ->withPivot('company_id')
            ->withTimestamps();
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
