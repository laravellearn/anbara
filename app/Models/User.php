<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\UserStatus;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'mobile',
        'last_ip',
        'password',
        'avatar',
        'email_verified_at',
        'mobile_verified_at',
        'status',
        'last_login_at',
        'last_ip',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'mobile_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'status' => UserStatus::class,
        'last_login_at' => 'datetime',

    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organizations()
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_user'
        )
            ->withPivot([
                'is_active',
                'joined_at'
            ])
            ->withTimestamps();
    }

    public function organizationMemberships()
    {
        return $this->hasMany(
            OrganizationUser::class
        );
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_user'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_user'
        );
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function otpCodes()
    {
        return $this->hasMany(OtpCode::class);
    }
}
