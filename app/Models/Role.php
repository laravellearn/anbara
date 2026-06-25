<?php

namespace App\Models;

use App\Concerns\Auditable;
use App\Concerns\BelongsToTenant;
use App\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes, BelongsToTenant, Auditable, LogsActivity;

    protected $fillable = [
        'tenant_id',
        'company_id',
        'code',
        'title',
        'description',
        'is_system',
        'is_active',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id');
    }
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'role_user'
        );
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
