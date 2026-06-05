<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CompanyScope;

class User extends Authenticatable
{
    use SoftDeletes;
    // use AutoFillTenantAndCompany; // اگر قصد پر کردن اتومات این دو فیلد باشد
    // use BelongsToTenant; // برای tenant scope

    //برای شرکت
    // protected static function booted(): void
    // {
    //     static::addGlobalScope(new CompanyScope());
    // }


    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'mobile',
        'last_ip',
        'password',
        'avatar',
        'email_verified_at',
        'mobile_verified_at',
        'last_login_at',
        'last_ip',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function otpCodes()
    {
        return $this->hasmany(otpCode::class);
    }

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id')
            ->withPivot('is_default')
            ->withTimestamps();
    }
    public function organizationalUnits()
    {
        return $this->belongsToMany(
            OrganizationalUnit::class,
            'organizational_unit_user'
        )->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user'
        )->withTimestamps();
    }

    public function hasPermission($permission)
    {
        $companyId = session('current_company_id');

        return $this->companies()
            ->where('companies.id', $companyId)
            ->whereHas('roles.permissions', function ($q) use ($permission) {
                $q->where('name', $permission);
            })
            ->exists();
    }

    public function hasRole($role)
    {
        $companyId = session('current_company_id');

        return $this->companies()
            ->where('companies.id', $companyId)
            ->whereHas('roles', function ($q) use ($role) {
                $q->where('code', $role);
            })
            ->exists();
    }
}
