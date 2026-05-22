<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    use HasFactory;

    protected $table = 'user_access';

    protected $fillable = [
        'user_id',
        'menu_access',
        'can_create',
        'can_update',
        'can_delete',
        'can_download',
        'can_view_detail',
        'can_monitor',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_download' => 'boolean',
        'can_view_detail' => 'boolean',
        'can_monitor' => 'boolean',
    ];

    /**
     * Relasi ke User (pemilik akses)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke User (creator)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (updater)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan menu
     */
    public function scopeForMenu($query, $menu)
    {
        return $query->where('menu_access', $menu);
    }

    /**
     * Check apakah user punya akses tertentu
     */
    public function hasPermission($permission)
    {
        $permissionColumn = 'can_' . $permission;

        if (!in_array($permissionColumn, $this->fillable)) {
            return false;
        }

        return $this->$permissionColumn;
    }
}