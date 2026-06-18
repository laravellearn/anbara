<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'item_id', 'barcode', 'is_default'];
}