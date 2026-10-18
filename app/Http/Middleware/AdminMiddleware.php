<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan berstatus is_admin == true
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // Jika bukan admin, tendang kembali ke dashboard utama dengan pesan error
        abort(403, 'Akses ditolak. Halaman ini khusus Administrator sistem.');
    }
}