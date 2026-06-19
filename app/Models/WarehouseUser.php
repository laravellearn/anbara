<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Relations\Pivot;

class WarehouseUser extends Pivot
{
    use Auditable,LogsActivity;
    protected $table = 'warehouse_user';

    protected $fillable = [
        'tenant_id', 'warehouse_id', 'user_id', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}