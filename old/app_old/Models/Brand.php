<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use SoftDeletes;
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',

        'title',

        'description',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(
            Product::class
        );
    }
}