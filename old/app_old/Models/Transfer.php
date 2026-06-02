<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TransferStatus;

class Transfer extends Model
{
    use SoftDeletes;
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'from_warehouse_id',
        'to_warehouse_id',

        'requested_by',

        'approved_by',
        'rejected_by',

        'transfer_number',

        'request_date',

        'approved_at',
        'received_at',

        'status',

        'description'
    ];

    protected $casts = [

        'request_date' => 'date',

        'approved_at' => 'datetime',

        'received_at' => 'datetime',
        'status' => TransferStatus::class,
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(
            Warehouse::class,
            'from_warehouse_id'
        );
    }

    public function toWarehouse()
    {
        return $this->belongsTo(
            Warehouse::class,
            'to_warehouse_id'
        );
    }

    public function requester()
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    public function rejector()
    {
        return $this->belongsTo(
            User::class,
            'rejected_by'
        );
    }

    public function items()
    {
        return $this->hasMany(
            TransferItem::class
        );
    }

    public function approvals()
    {
        return $this->hasMany(
            TransferApproval::class
        );
    }
}