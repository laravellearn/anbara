<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id', 'company_id', 'category_id', 'unit_id', 'name', 'sku',
        'barcode', 'description', 'min_stock', 'max_stock', 'image', 'attributes', 'is_active'
    ];
    protected $casts = ['attributes' => 'array', 'is_active' => 'boolean'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class);
    }

    public function alternatives()
    {
        return $this->belongsToMany(Item::class, 'item_alternatives', 'item_id', 'alternative_item_id')
                    ->withPivot('tenant_id');
    }

    public function packagings()
    {
        return $this->hasMany(ProductPackaging::class);
    }
}