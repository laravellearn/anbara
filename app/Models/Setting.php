<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use App\Concerns\BelongsToCompany;
use App\Concerns\AutoFillTenantAndCompany;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use BelongsToTenant, BelongsToCompany, AutoFillTenantAndCompany,LogsActivity;

    protected $fillable = [
        'tenant_id', 'company_id', 'group', 'key', 'type', 'value',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}