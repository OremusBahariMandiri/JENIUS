<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AccessControlServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Blade Directives
        Blade::if('canAccess', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasAccessToMenu($menu);
        });

        Blade::if('canCreate', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'create');
        });

        Blade::if('canUpdate', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'update');
        });

        Blade::if('canDelete', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'delete');
        });

        Blade::if('canDownload', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'download');
        });

        Blade::if('canViewDetail', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'view_detail');
        });

        Blade::if('canMonitor', function ($menu) {
            $user = auth()->user();
            if (!$user) return false;
            if ($user->is_admin) return true;
            return $user->hasPermission($menu, 'monitor');
        });
    }
}