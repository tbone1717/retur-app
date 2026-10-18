<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Http;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan alias 'admin' di sini
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class, // Sesuaikan dengan nama class middleware admin Anda
            'app.maintenance' => \App\Http\Middleware\AppMaintenance::class,
            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');
            
            if ($token && $chatId && !$e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                $pesan = "🚨 *SYSTEM ERROR TERDETEKSI*\n\n" .
                         "❌ Pesan: " . $e->getMessage() . "\n" .
                         "📂 File: " . basename($e->getFile()) . "\n" .
                         "📍 Baris: " . $e->getLine() . "\n" .
                         "⏰ Waktu: " . now()->format('d-m-Y H:i:s');

                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => substr($pesan, 0, 4000),
                    'parse_mode' => 'Markdown',
                ]);
            }
        });
    })->create();