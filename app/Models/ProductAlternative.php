<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProductAlternative extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'item_id', 'alternative_item_id'];
}