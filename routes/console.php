<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Http;

Schedule::call(function () {
    $token = env('TELEGRAM_BOT_TOKEN');
    $chatId = env('TELEGRAM_CHAT_ID');
    $dbPath = database_path('database.sqlite');

    if (file_exists($dbPath) && $token && $chatId) {
        Http::attach(
            'document', file_get_contents($dbPath), 'backup_retur_'.date('d-m-Y').'.sqlite'
        )->post("https://api.telegram.org/bot{$token}/sendDocument", [
            'chat_id' => $chatId,
            'caption' => "📦 *Backup Database Harian*\nSeluruh data telah diamankan.\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'),
            'parse_mode' => 'Markdown'
        ]);
    }
})->dailyAt('23:59'); // Berjalan otomatis setiap jam 23:59 malam