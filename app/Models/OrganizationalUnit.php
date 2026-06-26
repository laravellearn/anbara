<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use App\Scopes\CompanyScope; // اگه ساختی

class OrganizationalUnit extends Model
{
    use BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes, BelongsToTenant, Auditable, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'parent_id',
        'name',
        'code',
        'description',
        'manager_user_id',
        'is_active',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }
}
