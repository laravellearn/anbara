<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'code',
        'type',
        'national_id',
        'economic_code',
        'is_active',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent()
    {
        return $this->belongsTo(Company::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Company::class, 'parent_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'company_user'
        )->withPivot(['is_primary'])->withTimestamps();
    }

    public function organizationalUnits()
    {
        return $this->hasMany(OrganizationalUnit::class);
    }
}