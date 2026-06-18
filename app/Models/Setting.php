<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany;

    protected $fillable = [
        'tenant_id', 'company_id', 'group', 'key', 'type', 'value',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}