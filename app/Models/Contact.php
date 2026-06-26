<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany, SoftDeletes, LogsActivity, Auditable;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'type',
        'first_name',
        'last_name',
        'country_id',
        'province_id',
        'city',
        'company_name',
        'national_code',
        'economic_code',
        'mobile',
        'phone',
        'email',
        'website',
        'address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type'       => \App\Enums\ContactType::class, // اضافه شد
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }
}
