<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes,Auditable,LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'code', 'title', 'description',
        'address', 'allow_negative_stock', 'is_active',
    ];

    protected $casts = [
        'allow_negative_stock' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'warehouse_user')
                    ->using(WarehouseUser::class)
                    ->withPivot('is_default')
                    ->withTimestamps();
    }

    public function locations()
    {
        return $this->hasMany(WarehouseLocation::class);
    }
}