<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationUser extends Model
{
    protected $table = 'organization_user';

    protected $fillable = [
        'organization_id',
        'user_id',
        'is_active',
        'joined_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'joined_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(
            Organization::class
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'organization_user_role'
        );
    }
}