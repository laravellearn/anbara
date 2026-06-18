<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'warehouse_id', 'parent_id', 'code', 'name', 'type', 'capacity', 'is_active'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function parent()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_id');
    }
}