<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_code',
        'employee_id_number',
        'full_name',
        'department',
        'position',
        'work_location',
        'password',
        'is_admin',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Boot function untuk generate employee_code otomatis
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->employee_code)) {
                $user->employee_code = 'EMP-' . strtoupper(Str::random(8));
            }
        });
    }

    public function userAccess()
    {
        return $this->hasMany(UserAccess::class);
    }

    /**
     * Check apakah user punya akses ke menu tertentu
     */
    public function hasAccessToMenu($menu)
    {
        // Admin punya akses ke semua
        if ($this->is_admin) {
            return true;
        }

        return $this->userAccess()->where('menu_access', $menu)->exists();
    }

    /**
     * Check akses dengan nama action (monitoring, detail, tambah, ubah, hapus)
     */
    public function hasAccess($menu, $action)
    {
        // Admin punya semua akses
        if ($this->is_admin) {
            return true;
        }

        $access = $this->userAccess()->where('menu_access', $menu)->first();

        if (!$access) {
            return false;
        }

        // Map action ke column name
        $actionMap = [
            'monitoring' => 'can_monitor',
            'index' => 'can_monitor', // index = monitoring
            'detail' => 'can_view_detail',
            'tambah' => 'can_create',
            'ubah' => 'can_update',
            'hapus' => 'can_delete',
            'download' => 'can_download',
        ];

        $column = $actionMap[$action] ?? null;

        if (!$column) {
            return false;
        }

        return $access->$column ?? false;
    }

    /**
     * Alternative: hasPermission untuk compatibility
     */
    public function hasPermission($menu, $permission)
    {
        if ($this->is_admin) {
            return true;
        }

        $access = $this->userAccess()->where('menu_access', $menu)->first();

        if (!$access) {
            return false;
        }

        $permissionColumn = 'can_' . $permission;
        return $access->$permissionColumn ?? false;
    }
}
