<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Scopes\CompanyScope;
use App\Services\TenantManager;
use Illuminate\Database\Eloquent\Collection;

class User extends Authenticatable
{
    use SoftDeletes,Auditable;
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
        if (!$companyId)
            return new Collection();

        $companyUser = $this->companyUsers()->where('company_id', $companyId)->first();
        return $companyUser ? $companyUser->roles : new Collection();
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


    /**
     * بررسی سوپر ادمین بودن
     * کاربری که tenant_id نال دارد = سوپر ادمین
     */
    public function isSuperAdmin(): bool
    {
        return is_null($this->tenant_id);
    }

    /**
     * بررسی ادمین سازمان بودن
     */
    public function isTenantAdmin(): bool
    {
        $companyId = app(\App\Services\TenantManager::class)->getCompanyId();

        if (!$companyId) {
            return false;
        }

        return \DB::table('company_user_role')
            ->join('company_user', 'company_user_role.company_user_id', '=', 'company_user.id')
            ->join('roles', 'company_user_role.role_id', '=', 'roles.id')
            ->where('company_user.user_id', $this->id)
            ->where('company_user.company_id', $companyId)
            ->where('roles.code', 'tenant_admin')
            ->exists();
    }

    /**
     * دسترسی‌های مستقیم کاربر (بدون نقش)
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    /**
     * دریافت نام نقش کاربر
     */
    public function getCurrentRoleName()
    {
        // ۱. سوپر ادمین (tenant_id نال)
        if ($this->isSuperAdmin()) {
            return 'مدیر کل سامانه';
        }

        $companyId = app(\App\Services\TenantManager::class)->getCompanyId();

        if (!$companyId) {
            return 'سازمان انتخاب نشده';
        }

        // ۲. کاربر عادی - خواندن نقش از دیتابیس
        $role = \DB::table('company_user_role')
            ->join('company_user', 'company_user_role.company_user_id', '=', 'company_user.id')
            ->join('roles', 'company_user_role.role_id', '=', 'roles.id')
            ->where('company_user.user_id', $this->id)
            ->where('company_user.company_id', $companyId)
            ->select('roles.*')
            ->first();

        if ($role) {
            return $role->title;
        }

        return 'نقش تخصیص نیافته';
    }

    /**
     * دریافت آبجکت نقش کاربر
     */
    public function getCurrentRole()
    {
        // سوپر ادمین - نقش مجازی
        if ($this->isSuperAdmin()) {
            return (object) [
                'id' => 0,
                'code' => 'super_admin',
                'title' => 'مدیر کل سامانه',
                'is_system' => true,
            ];
        }

        $companyId = app(\App\Services\TenantManager::class)->getCompanyId();

        if (!$companyId) {
            return null;
        }

        return \DB::table('company_user_role')
            ->join('company_user', 'company_user_role.company_user_id', '=', 'company_user.id')
            ->join('roles', 'company_user_role.role_id', '=', 'roles.id')
            ->where('company_user.user_id', $this->id)
            ->where('company_user.company_id', $companyId)
            ->select('roles.*')
            ->first();
    }

    /**
     * دریافت همه نقش‌های کاربر با سازمان‌های مربوطه
     */
    public function getRolesWithCompanies()
    {
        // سوپر ادمین همه نقش‌ها را نمی‌بینیم
        if ($this->isSuperAdmin()) {
            return collect([
                0 => collect([
                    (object) [
                        'id' => 0,
                        'code' => 'super_admin',
                        'title' => 'مدیر کل سامانه',
                    ]
                ])
            ]);
        }

        $companyUsers = \DB::table('company_user')
            ->where('user_id', $this->id)
            ->get();

        $result = collect();

        foreach ($companyUsers as $cu) {
            $roles = \DB::table('company_user_role')
                ->join('roles', 'company_user_role.role_id', '=', 'roles.id')
                ->where('company_user_role.company_user_id', $cu->id)
                ->select('roles.*')
                ->get();

            if ($roles->isNotEmpty()) {
                $result[$cu->company_id] = $roles;
            }
        }

        return $result;
    }

    /**
     * بررسی می‌کند که آیا کاربر مالک (Owner) مستأجر است یا خیر.
     * مالک کسی است که در سازمان ریشه (parent_id = null) نقش admin داشته باشد.
     */
    // app/Models/User.php
    // app/Models/User.php

    public function companyUsers()
    {
        return $this->hasMany(CompanyUser::class);
    }


    public function isOwner(): bool
    {
        $tenant = $this->tenant;
        if (!$tenant) return false;

        $rootCompany = $tenant->rootCompany;
        if (!$rootCompany) return false;

        $companyUser = $this->companyUsers()
            ->where('company_id', $rootCompany->id)
            ->first();

        if (!$companyUser) return false;

        return $companyUser->roles()
            ->where('code', 'tenant_admin') // ← تغییر به code
            ->exists();
    }
}
