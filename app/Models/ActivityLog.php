<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use BelongsToTenant;

    protected $table = 'activity_logs';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'user_id',
        'action',
        'subject_type',
        'subject_id',
        'ip_address',
        'old_values',
        'new_values',
        'description',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // رابطه با Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // رابطه با Company (ممکن است null باشد)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // رابطه با User (ممکن است null باشد)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // رابطهٔ پلی‌مورفیک برای subject (موجودی که عملیات روی آن انجام شده)
    public function subject()
    {
        return $this->morphTo(__FUNCTION__, 'subject_type', 'subject_id');
    }
}