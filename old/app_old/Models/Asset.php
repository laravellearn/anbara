<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use AssetStatus;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'product_id',

        'warehouse_id',

        'asset_code',

        'serial_number',

        'title',

        'purchase_date',

        'purchase_price',

        'status',

        'description'
    ];

    protected $casts = [

        'status' => AssetStatus::class,

        'purchase_date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function assignments()
    {
        return $this->hasMany(
            AssetAssignment::class
        );
    }

    public function repairs()
    {
        return $this->hasMany(
            AssetRepair::class
        );
    }

    public function scraps()
    {
        return $this->hasMany(
            AssetScrap::class
        );
    }

    public function histories()
    {
        return $this->hasMany(
            AssetHistory::class
        );
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
