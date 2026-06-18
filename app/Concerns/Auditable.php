<?php

namespace App\Concerns;

trait Auditable
{
    public static function bootAuditable(): void
    {
        static::updating(function ($model) {
            $model->edited_by = auth()->check() ? auth()->id() : null;
        });

        static::deleting(function ($model) {
            if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
                $model->deleted_by = auth()->check() ? auth()->id() : null;
                $model->saveQuietly();
            }
        });
    }
}