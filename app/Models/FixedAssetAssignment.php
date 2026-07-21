<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedAssetAssignment extends Model
{
    protected $fillable = [
        'tenant_id', 'fixed_asset_id', 'employee_id', 'assigned_by',
        'assigned_at', 'returned_at', 'status', 'notes',
    ];

    protected $casts = [
        'assigned_at'  => 'date',
        'returned_at'  => 'date',
    ];

    public function fixedAsset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
