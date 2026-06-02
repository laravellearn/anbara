<?php

namespace App\Traits;

use App\Models\OrganizationUser;

trait HasPermissions
{
    public function hasPermission(
        string $permission,
        ?int $organizationId = null
    ): bool {

        $organizationId ??= organizationId();

        $membership = OrganizationUser::query()
            ->where('organization_id', $organizationId)
            ->where('user_id', $this->id)
            ->first();

        if (! $membership) {
            return false;
        }

        return $membership->roles()
            ->whereHas(
                'permissions',
                fn ($q) => $q->where(
                    'name',
                    $permission
                )
            )
            ->exists();
    }
}