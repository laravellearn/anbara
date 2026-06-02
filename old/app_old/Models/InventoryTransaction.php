<?php

namespace App\Models;

use App\Enums\InventoryTransactionStatus;
use App\Traits\BelongsToTenant;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use InventoryTransactionType;

class InventoryTransaction extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'warehouse_id',

        'user_id',

        'type',

        'reference_type',
        'reference_id',

        'transaction_date',

        'description',

        'status',
    ];

    protected $casts = [

        'transaction_date' => 'datetime',
        'type' => InventoryTransactionType::class,

        'status' => InventoryTransactionStatus::class,
    ];

    public function warehouse()
    {
        return $this->belongsTo(
            Warehouse::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function items()
    {
        return $this->hasMany(
            InventoryTransactionItem::class
        );
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
