<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProductPackaging extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'item_id', 'unit_id', 'name', 'quantity_per_unit'];
}