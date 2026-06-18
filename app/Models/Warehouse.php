<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use BelongsToTenant, SoftDeletes;
    protected $fillable = ['tenant_id', 'company_id', 'name', 'code', 'address', 'manager_user_id', 'capacity', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}