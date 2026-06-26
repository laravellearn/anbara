<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Province extends Model
{
    use SoftDeletes, LogsActivity, Auditable;

    protected $fillable = ['name', 'country_id','slug','lat','long','is_active'];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

}
