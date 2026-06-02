<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetScrap extends Model
{
    protected $fillable = [

        'asset_id',

        'scrap_date',

        'reason'
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}