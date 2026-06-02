<?php

namespace App\Traits;

use App\Models\OrganizationUser;

trait HasRoles
{
    public function hasRole(
        string $role,
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
            ->where('title', $role)
            ->exists();
    }
}