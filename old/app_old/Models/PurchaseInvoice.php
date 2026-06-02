<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Model;
use InvoiceStatus;

class PurchaseInvoice extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'contact_id',

        'invoice_number',

        'invoice_date',

        'total_amount',

        'discount_amount',

        'tax_amount',

        'final_amount',

        'status',

        'description',
    ];

    protected $casts = [

        'invoice_date' => 'date',

        'total_amount' => 'decimal:0',

        'discount_amount' => 'decimal:0',

        'tax_amount' => 'decimal:0',

        'final_amount' => 'decimal:0',
        'status' => InvoiceStatus::class,
    ];

    public function contact()
    {
        return $this->belongsTo(
            Contact::class
        );
    }

    public function items()
    {
        return $this->hasMany(
            PurchaseInvoiceItem::class
        );
    }

    public function inventoryTransactions()
    {
        return $this->morphMany(
            InventoryTransaction::class,
            'reference'
        );
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
