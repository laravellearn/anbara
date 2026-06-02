<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class MeasurementUnit extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',

        'parent_id',

        'title',
        'symbol',

        'conversion_factor',

        'description',

        'is_active',
    ];

    protected $casts = [
        'conversion_factor' => 'float',
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(
            MeasurementUnit::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            MeasurementUnit::class,
            'parent_id'
        );
    }

    public function products()
    {
        return $this->hasMany(
            Product::class,
            'measurement_unit_id'
        );
    }
}