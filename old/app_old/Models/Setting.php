<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use BelongsToTenant;
    use BelongsToOrganization;

    protected $fillable = [
        'tenant_id',
        'organization_id',
        'group',
        'key',
        'type',
        'value',
    ];

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

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getValueAttribute($value): mixed
    {
        return match ($this->type) {

            'integer' => (int) $value,

            'float' => (float) $value,

            'boolean' => filter_var(
                $value,
                FILTER_VALIDATE_BOOLEAN
            ),

            'json' => json_decode(
                $value,
                true
            ),

            default => $value,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setValueAttribute($value): void
    {
        if (is_array($value)) {

            $this->attributes['value'] = json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            );

            return;
        }

        $this->attributes['value'] = $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function get(
        string $key,
        mixed $default = null
    ): mixed {
        $setting = static::query()
            ->where('key', $key)
            ->first();

        return $setting
            ? $setting->value
            : $default;
    }

    public static function set(
        string $key,
        mixed $value,
        string $group = 'general'
    ): self {

        $type = match (true) {

            is_bool($value) => 'boolean',

            is_int($value) => 'integer',

            is_float($value) => 'float',

            is_array($value) => 'json',

            default => 'string',
        };

        return static::updateOrCreate(
            [
                'tenant_id' => tenantId(),
                'organization_id' => organizationId(),
                'key' => $key,
            ],
            [
                'group' => $group,
                'type' => $type,
                'value' => $value,
            ]
        );
    }
}