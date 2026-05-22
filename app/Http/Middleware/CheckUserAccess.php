<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menu
     * @param  string|null  $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $menu, $action = null): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Jika user adalah admin, berikan akses
        if ($user->is_admin) {
            return $next($request);
        }

        // Jika tidak ada action specified, hanya cek menu access
        if ($action === null) {
            if ($user->hasAccessToMenu($menu)) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki akses ke menu ini.');
        }

        // Cek apakah user memiliki akses untuk action tertentu
        if ($user->hasAccess($menu, $action)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki izin untuk melakukan aksi ini.');
    }
}