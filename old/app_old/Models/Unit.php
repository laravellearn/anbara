<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',

        'parent_id',

        'title',
        'description',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(
            Unit::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            Unit::class,
            'parent_id'
        );
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}