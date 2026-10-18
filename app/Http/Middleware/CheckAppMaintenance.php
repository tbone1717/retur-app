<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckAppMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika cache maintenance aktif
        if (Cache::has('app_custom_maintenance')) {
            // Izinkan jika user adalah admin
            if (auth()->check() && auth()->user()->is_admin) {
                return $next($request);
            }

            // Jika bukan admin, tampilkan halaman 503 / pesan maintenance
            abort(503, 'Sistem sedang dalam pemeliharaan berkala untuk user operasional.');
        }

        return $next($request);
    }
}