<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'category_id',

        'brand_id',

        'measurement_unit_id',

        'title',

        'sku',

        'barcode',

        'model',

        'description',

        'purchase_price',

        'sale_price',

        'minimum_stock',

        'is_asset',

        'is_active',
    ];

    protected $casts = [

        'purchase_price' => 'decimal:0',

        'sale_price' => 'decimal:0',

        'minimum_stock' => 'float',

        'is_asset' => 'boolean',

        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(
            Category::class
        );
    }

    public function brand()
    {
        return $this->belongsTo(
            Brand::class
        );
    }

    public function measurementUnit()
    {
        return $this->belongsTo(
            MeasurementUnit::class
        );
    }

    public function contacts()
    {
        return $this->belongsToMany(
            Contact::class,
            'contacts'
        );
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
