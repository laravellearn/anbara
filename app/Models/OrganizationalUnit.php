<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Concerns\BelongsToTenant;
use App\Scopes\CompanyScope; // اگه ساختی

class OrganizationalUnit extends Model
{
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'parent_id',
        'name',
        'code',
        'manager_user_id',
        'is_active',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'organizational_unit_user'
        )->withTimestamps();
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }


    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
