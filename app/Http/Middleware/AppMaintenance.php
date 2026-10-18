<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AppMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek jika status maintenance aktif (misal disimpan di cache/session)
        if (cache()->get('app_maintenance_mode', false)) {
            // Jika user biasa (bukan admin), kunci akses ke aplikasi
            if (auth()->check() && !auth()->user()->is_admin) {
                return response()->view('errors.503', [], 503);
            }
        }

        return $next($request);
    }
}