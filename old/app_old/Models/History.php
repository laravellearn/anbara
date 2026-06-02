<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToOrganization;
use HistoryAction;
use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'warehouse_id',

        'product_id',

        'inventory_transaction_id',

        'action',

        'quantity_before',

        'quantity_change',

        'quantity_after',

        'description',
    ];

    protected $casts = [

        'quantity_before' => 'float',

        'quantity_change' => 'float',

        'quantity_after' => 'float',
        'action' => HistoryAction::class,

    ];

    public function warehouse()
    {
        return $this->belongsTo(
            Warehouse::class
        );
    }

    public function product()
    {
        return $this->belongsTo(
            Product::class
        );
    }

    public function transaction()
    {
        return $this->belongsTo(
            InventoryTransaction::class,
            'inventory_transaction_id'
        );
    }
}
