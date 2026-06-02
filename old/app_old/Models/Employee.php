<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',

        'unit_id',

        'user_id',

        'employee_code',

        'first_name',
        'last_name',

        'national_code',

        'mobile',
        'phone',
        'email',

        'position',

        'employment_date',
        'termination_date',

        'employment_status',

        'address',

        'avatar',

        'description',

        'is_active',
    ];

    protected $casts = [
        'employment_date' => 'date',
        'termination_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function getFullNameAttribute()
    {
        return trim(
            $this->first_name.' '.$this->last_name
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}