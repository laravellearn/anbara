<?php

// app/Helpers/LogHelper.php
if (!function_exists('log_activity')) {
    function log_activity($action, $subjectType, $subjectId, $description = null)
    {
        $manager = app(\App\Services\TenantManager::class);
        \App\Models\ActivityLog::create([
            'tenant_id'   => $manager->getTenantId(),
            'company_id'  => $manager->getCompanyId(),
            'user_id'     => auth()->id(),
            'action'      => $action,
            'subject_type'=> $subjectType,
            'subject_id'  => $subjectId,
            'ip_address'  => request()->ip(),
            'description' => $description,
        ]);
    }
}