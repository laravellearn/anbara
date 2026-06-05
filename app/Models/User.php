<?php

namespace App\Models;

use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CompanyScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Collection;

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

    // اما چون هر کاربر می‌تواند در چند شرکت باشد، بهتر است نقش‌ها را از طریق company_user استخراج کنیم.
    // یک متد کمکی برای گرفتن نقش‌های کاربر در شرکت جاری:
    public function getRolesForCurrentCompany(): Collection
    {
        $manager = app(TenantManager::class);
        $companyId = $manager->getCompanyId();
        if (! $companyId) return new Collection();

        $companyUser = $this->companyUsers()->where('company_id', $companyId)->first();
        return $companyUser ? $companyUser->roles : new Collection();
    }

    // رابطه‌ها
    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id')
            ->withPivot('is_default', 'id') // id pivot برای ارجاع
            ->withTimestamps();
    }

    public function organizationalUnits()
    {
        return $this->belongsToMany(
            OrganizationalUnit::class,
            'organizational_unit_user'
        )->withTimestamps();
    }

    // رابطه مستقیم با Role از طریق company_user و company_user_role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'company_user_role', 'company_user_id', 'role_id')
            ->withTimestamps();
    }

    public function defaultCompany()
    {
        return $this->belongsToMany(Company::class, 'company_user', 'user_id', 'company_id')
            ->wherePivot('is_default', true)
            ->withPivot('is_default')
            ->first();
    }

    // App\Models\User.php
    public function isSuperAdmin(): bool
    {
        return is_null($this->tenant_id);
    }

    /**
     * آیا کاربر در شرکت جاری (Current Company) نقش مدیر سازمان (tenant_admin) دارد؟
     */
    public function isTenantAdmin(): bool
    {
        /** @var \App\Services\TenantManager $manager */
        $manager = app(\App\Services\TenantManager::class);
        $companyId = $manager->getCompanyId();

        if (! $companyId) {
            return false;
        }

        return $this->companyUsers()
            ->where('company_id', $companyId)
            ->whereHas('roles', function ($query) {
                $query->where('code', 'tenant_admin');
            })
            ->exists();
    }
}
