<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductMeasurementUnit extends Pivot
{
    use Auditable,LogsActivity;
    protected $table = 'product_measurement_units';

    protected $fillable = [
        'tenant_id', 'product_id', 'measurement_unit_id', 'conversion_factor', 'is_default', 'company_id',
    ];

    protected $casts = [
        'conversion_factor' => 'decimal:6',
        'is_default' => 'boolean',
    ];
}