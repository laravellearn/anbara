<?php

namespace App\Concerns;

use App\Models\ActivityLog;
use App\Services\TenantManager;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        // ایجاد
        static::created(function ($model) {
            static::log('created', $model);
        });

        // ویرایش
        static::updated(function ($model) {
            static::log('updated', $model);
        });

        // حذف (soft delete)
        static::deleted(function ($model) {
            static::log('deleted', $model);
        });
    }

    protected static function log(string $action, $model): void
    {
        if (!auth()->check()) return; // بدون کاربر لاگ نمی‌کنیم (مثلاً در سیدر)

        $manager = app(TenantManager::class);

        $data = [
            'tenant_id'    => $manager->getTenantId(),
            'company_id'   => $manager->getCompanyId(),
            'user_id'      => auth()->id(),
            'action'       => $action,
            'subject_type' => get_class($model),
            'subject_id'   => $model->getKey(),
            'ip_address'   => request()->ip(),
            'description'  => static::makeDescription($action, $model),
            'old_values'   => $action === 'updated' ? json_encode($model->getOriginal()) : null,
            'new_values'   => in_array($action, ['created', 'updated']) ? json_encode($model->getAttributes()) : null,
        ];

        ActivityLog::create($data);
    }

    protected static function makeDescription(string $action, $model): string
    {
        $modelName = class_basename($model);
        $nameField = $model->name ?? $model->title ?? $model->id;

        return match ($action) {
            'created' => "{$modelName} جدید ایجاد شد: {$nameField}",
            'updated' => "{$modelName} ویرایش شد: {$nameField}",
            'deleted' => "{$modelName} حذف شد: {$nameField}",
            default   => "عملیات {$action} روی {$modelName} انجام شد.",
        };
    }
}