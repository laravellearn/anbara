<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use BelongsToTenant;
    protected $fillable = ['tenant_id', 'parent_id', 'name', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }
}