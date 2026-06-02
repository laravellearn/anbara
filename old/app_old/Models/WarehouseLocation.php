<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',

        'warehouse_id',

        'title',
        'code',

        'parent_id',

        'description',
    ];

    public function warehouse()
    {
        return $this->belongsTo(
            Warehouse::class
        );
    }

    public function parent()
    {
        return $this->belongsTo(
            WarehouseLocation::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            WarehouseLocation::class,
            'parent_id'
        );
    }
}