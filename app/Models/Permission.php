<?php

namespace App\Models;

use App\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes,Auditable;

    protected $fillable = [
        'name',
        'title',
        'description',
        'is_active',
        'group'
    ];

    // متد برای گروه‌بندی خودکار
    public static function getGroupedPermissions()
    {
        $permissions = self::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = $permission->group ?? 'سایر';
            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'permission_role'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'permission_user'
        );
    }
}