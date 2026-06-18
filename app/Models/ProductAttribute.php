<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'name', 'type', 'options'];
    protected $casts = ['options' => 'array'];
}