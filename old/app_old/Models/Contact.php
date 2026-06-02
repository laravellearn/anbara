<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use ContactType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    use SoftDeletes;
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [

        'tenant_id',
        'organization_id',

        'type',

        'first_name',
        'last_name',

        'company_name',

        'national_code',
        'economic_code',

        'mobile',
        'phone',
        'email',

        'website',

        'address',

        'description',

        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'type' => ContactType::class,
    ];

    protected $appends = [
        'display_name',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getDisplayNameAttribute(): string
    {
        if ($this->type === 'company') {
            return (string) $this->company_name;
        }

        return trim(
            ($this->first_name ?? '') . ' ' .
            ($this->last_name ?? '')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function contactTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ContactType::class,
            'contact_contact_type'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Invoices
    |--------------------------------------------------------------------------
    */

    public function purchaseInvoices(): HasMany
    {
        return $this->hasMany(
            PurchaseInvoice::class
        );
    }

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(
            SalesInvoice::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeCompanies($query)
    {
        return $query->where('type', 'company');
    }

    public function scopePersons($query)
    {
        return $query->where('type', 'person');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isCompany(): bool
    {
        return $this->type === 'company';
    }

    public function isPerson(): bool
    {
        return $this->type === 'person';
    }
}