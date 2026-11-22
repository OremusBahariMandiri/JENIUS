<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    /**
     * Catat aktivitas create
     */
    protected function logCreate(string $module, mixed $model, string $description = ''): void
    {
        ActivityLog::record(
            action: 'create',
            description: $description ?: "{$module} successfully created.",
            module: $module,
            modelName: class_basename($model),
            recordId: $model->id ?? null,
            newValues: $this->getLoggableAttributes($model),
        );
    }

    /**
     * Catat aktivitas update
     */
    protected function logUpdate(string $module, mixed $model, array $original, string $description = ''): void
    {
        ActivityLog::record(
            action: 'update',
            description: $description ?: "{$module} successfully updated.",
            module: $module,
            modelName: class_basename($model),
            recordId: $model->id ?? null,
            oldValues: $original,
            newValues: $this->getLoggableAttributes($model),
        );
    }

    /**
     * Catat aktivitas delete
     */
    protected function logDelete(string $module, mixed $model, string $description = ''): void
    {
        ActivityLog::record(
            action: 'delete',
            description: $description ?: "{$module} successfully deleted.",
            module: $module,
            modelName: class_basename($model),
            recordId: $model->id ?? null,
            oldValues: $this->getLoggableAttributes($model),
        );
    }

    /**
     * Catat aktivitas restore
     */
    protected function logRestore(string $module, mixed $model, string $description = ''): void
    {
        ActivityLog::record(
            action: 'restore',
            description: $description ?: "{$module} successfully restored.",
            module: $module,
            modelName: class_basename($model),
            recordId: $model->id ?? null,
        );
    }

    /**
     * Ambil atribut model yang bisa di-log (exclude hidden & password)
     */
    private function getLoggableAttributes(mixed $model): array
    {
        if (!is_object($model) || !method_exists($model, 'getAttributes')) {
            return [];
        }

        $hidden   = array_merge($model->getHidden(), ['password', 'remember_token']);
        $attrs    = $model->getAttributes();

        return array_diff_key($attrs, array_flip($hidden));
    }
}
