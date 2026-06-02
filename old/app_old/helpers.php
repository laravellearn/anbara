<?php

if (! function_exists('tenantId')) {

    function tenantId(): ?int
    {
        return app()->bound('currentTenantId')
            ? app('currentTenantId')
            : null;
    }
}

if (! function_exists('organizationId')) {

    function organizationId(): ?int
    {
        return app()->bound('currentOrganizationId')
            ? app('currentOrganizationId')
            : null;
    }
}