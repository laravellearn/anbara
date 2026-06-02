<?php

namespace App\Models;

use App\Enums\AssetAssignmentStatus;
use Illuminate\Database\Eloquent\Model;

class AssetAssignment extends Model
{
    protected $fillable = [

        'asset_id',

        'employee_id',

        'assigned_at',

        'returned_at',

        'description'
    ];

protected function casts(): array
{
    return [
        'status' => AssetAssignmentStatus::class,

        'assigned_at' => 'datetime',

        'returned_at' => 'datetime',
    ];
}

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
