<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class CompanyUser extends Model
{
    use BelongsToTenant,Auditable,LogsActivity;

    protected $table = 'company_user';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'user_id',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // رابطه با کاربر
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // رابطه با شرکت
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // رابطه با نقش‌ها از طریق جدول company_user_role
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'company_user_role', 'company_user_id', 'role_id')
                    ->withTimestamps();
    }
}