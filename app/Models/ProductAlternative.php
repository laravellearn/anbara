<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductAlternative extends Pivot
{
    protected $table = 'product_alternatives';

    protected $fillable = [
        'tenant_id', 'product_id', 'alternative_product_id', 'company_id',
    ];
}