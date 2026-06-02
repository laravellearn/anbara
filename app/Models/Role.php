<?php

namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function organizationUsers()
    {
        return $this->belongsToMany(
            OrganizationUser::class,
            'organization_user_role'
        );
    }

    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'role_user'
        );
    }

    public function organizations()
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_user'
        )
            ->withPivot('role_id');
    }
}
