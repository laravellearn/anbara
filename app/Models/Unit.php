<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'title', 'symbol', 'is_active'];
}