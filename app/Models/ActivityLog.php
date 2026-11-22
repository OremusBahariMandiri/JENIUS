<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'employee_id_number',
        'action',
        'module',
        'model_name',
        'record_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Label warna badge berdasarkan action
     */
    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'login'   => 'success',
            'logout'  => 'secondary',
            'create'  => 'primary',
            'update'  => 'warning',
            'delete'  => 'danger',
            'restore' => 'info',
            'view'    => 'light',
            default   => 'dark',
        };
    }

    /**
     * Label teks berdasarkan action
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'login'   => 'Login',
            'logout'  => 'Logout',
            'create'  => 'Create',
            'update'  => 'Update',
            'delete'  => 'Delete',
            'restore' => 'Restore',
            'view'    => 'View',
            default   => ucfirst($this->action),
        };
    }

    /**
     * Helper statis untuk mencatat aktivitas
     */
    public static function record(
        string $action,
        string $description,
        ?string $module = null,
        ?string $modelName = null,
        mixed $recordId = null,
        ?array $oldValues = null,
        ?array $newValues = null
    ): self {
        $user = auth()->user();
        $request = request();

        return self::create([
            'user_id'            => $user?->id,
            'user_name'          => $user?->full_name ?? $user?->name,
            'employee_id_number' => $user?->employee_id_number,
            'action'             => $action,
            'module'             => $module,
            'model_name'         => $modelName,
            'record_id'          => $recordId ? (string) $recordId : null,
            'description'        => $description,
            'old_values'         => $oldValues,
            'new_values'         => $newValues,
            'ip_address'         => $request?->ip(),
            'user_agent'         => $request?->userAgent(),
            'url'                => $request?->fullUrl(),
            'method'             => $request?->method(),
        ]);
    }
}
