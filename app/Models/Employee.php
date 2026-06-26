<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes, Auditable, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'organizational_unit_id',
        'user_id',
        'contact_id',
        'employee_code',
        'name',
        'national_code',
        'mobile',
        'phone',
        'position',
        'employment_date',
        'address',
        'description',
        'is_active',
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    protected $casts = [
        'employment_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function organizationalUnit()
    {
        return $this->belongsTo(OrganizationalUnit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
