<?php

namespace App\Models;

use AssetRepairStatus;
use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    protected $fillable = [

        'asset_id',

        'repair_date',

        'cost',

        'description'
    ];

    protected $casts = [

    'status' => AssetRepairStatus::class,

    'repair_date' => 'date',

    'completed_at' => 'datetime',
];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}