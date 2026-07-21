<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TenantSetting extends Model
{
    use BelongsToTenant;

    protected $table = 'settings';

    protected $fillable = [
        'tenant_id', 'company_id', 'group', 'key', 'type', 'value',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function getValueAttribute($raw): mixed
    {
        return match ($this->type) {
            'integer' => (int) $raw,
            'float'   => (float) $raw,
            'boolean' => filter_var($raw, FILTER_VALIDATE_BOOLEAN),
            'json'    => json_decode($raw, true),
            default   => $raw,
        };
    }

    public function setValueAttribute(mixed $val): void
    {
        $this->attributes['value'] = is_array($val) ? json_encode($val) : $val;
    }

    /**
     * دریافت مقدار یک کلید از تنظیمات
     */
    public static function get(int $tenantId, string $key, mixed $default = null): mixed
    {
        $setting = static::where('tenant_id', $tenantId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * ذخیره یا به‌روزرسانی یک کلید
     */
    public static function set(int $tenantId, int $companyId, string $group, string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['tenant_id' => $tenantId, 'company_id' => $companyId, 'key' => $key],
            ['group' => $group, 'type' => $type, 'value' => is_array($value) ? json_encode($value) : $value]
        );
    }
}
