<?php

if (!function_exists('can_access')) {
    function can_access($menu)
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAccessToMenu($menu);
    }
}

if (!function_exists('has_access')) {
    function has_access($menu, $action)
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->hasAccess($menu, $action);
    }
}

if (!function_exists('can_tambah')) {
    function can_tambah($menu)
    {
        return has_access($menu, 'tambah');
    }
}

if (!function_exists('can_ubah')) {
    function can_ubah($menu)
    {
        return has_access($menu, 'ubah');
    }
}

if (!function_exists('can_hapus')) {
    function can_hapus($menu)
    {
        return has_access($menu, 'hapus');
    }
}

if (!function_exists('can_detail')) {
    function can_detail($menu)
    {
        return has_access($menu, 'detail');
    }
}

if (!function_exists('can_download')) {
    function can_download($menu)
    {
        return has_access($menu, 'download');
    }
}

if (!function_exists('can_monitoring')) {
    function can_monitoring($menu)
    {
        return has_access($menu, 'monitoring');
    }
}