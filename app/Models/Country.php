<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
        use SoftDeletes,LogsActivity,Auditable;

    protected $fillable = ['name', 'slug','lat','long','is_active']; // ساختار دلخواه

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }
}