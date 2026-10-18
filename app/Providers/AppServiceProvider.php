<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;

// DAFTAR MODEL
use App\Models\Retur; 
use App\Models\Produk;
use App\Models\B2b;
use App\Models\ReturBS;
use App\Models\Pemusnahan;
use App\Models\Karung;

// OBSERVER
use App\Observers\ReturObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. NOTIFIKASI SISTEM & KEAMANAN LOGIN
        if (!cache()->has('app_started_notified')) {
            self::sendTelegram("🟢 *RETUR SYSTEM ONLINE*\n\n🚀 Aplikasi berhasil diakses dan berjalan normal di server.\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
            cache()->put('app_started_notified', true, now()->addHours(6));
        }

        Event::listen(function (Login $event) {
            $user = $event->user;
            $ip = self::getRealIp();
            $lokasi = self::getLokasi($ip);
            $device = request()->userAgent() ?? 'Perangkat tidak diketahui';

            self::sendTelegram("🔓 *LOGIN BERHASIL*\n\n👤 Nama: {$user->name}\n🏢 Depo: {$user->depo}\n🌐 IP Address: {$ip}\n📍 Lokasi: {$lokasi}\n💻 Perangkat: {$device}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        Event::listen(function (Failed $event) {
            $target = $event->credentials['userid'] ?? $event->credentials['email'] ?? 'Tidak diketahui';
            $ip = self::getRealIp();
            $lokasi = self::getLokasi($ip);
            $device = request()->userAgent() ?? 'Perangkat tidak diketahui';

            self::sendTelegram("⚠️ *PERCOBAAN LOGIN GAGAL*\n\n📧 Target: {$target}\n🌐 IP Address: {$ip}\n📍 Lokasi: {$lokasi}\n💻 Perangkat: {$device}\n🚨 Status: Salah sandi atau ID tidak terdaftar.\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        // 2. AKTIVITAS RETUR (MENGGUNAKAN OBSERVER)
        Retur::observe(ReturObserver::class);

        // 3. AKTIVITAS PRODUK
        Produk::created(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("🆕 *PRODUK BARU*\n\n👤 Oleh: {$user}\n📦 SKU: {$item->sku}\n🏷 Keterangan: {$item->keterangan}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
        Produk::deleted(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("❌ *PRODUK DIHAPUS*\n\n👤 Oleh: {$user}\n📦 SKU: {$item->sku}\n🏷️ Keterangan: {$item->keterangan}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        // 4. AKTIVITAS B2B
        B2b::created(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            $toko = $item->nama_toko ?? '-';
            self::sendTelegram("🏢 *INPUT B2B BARU*\n\n👤 Oleh: {$user}\n🏪 Toko: {$toko}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
        B2b::deleted(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            $toko = $item->nama_toko ?? '-';
            self::sendTelegram("🗑️ *DATA B2B DIHAPUS*\n\n👤 Oleh: {$user}\n🏪 Toko: {$toko}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        // 5. AKTIVITAS RETUR BS
        ReturBS::created(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("⚠️ *INPUT RETUR BS BARU*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
        ReturBS::deleted(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("🗑️ *RETUR BS DIHAPUS*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        // 6. AKTIVITAS PEMUSNAHAN
        Pemusnahan::created(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("🔥 *PEMUSNAHAN BARU DICATAT*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
        Pemusnahan::deleted(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("🗑️ *DATA PEMUSNAHAN DIHAPUS*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });

        // 7. AKTIVITAS KARUNG
        Karung::created(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("💰 *DATA KARUNG BARU*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
        Karung::deleted(function ($item) {
            $user = auth()->user()?->name ?? 'Sistem';
            self::sendTelegram("🗑️ *DATA KARUNG DIHAPUS*\n\n👤 Oleh: {$user}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
        });
    }

    private static function sendTelegram($pesan)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if ($token && $chatId) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $pesan,
                'parse_mode' => 'Markdown',
            ]);
        }
    }

    private static function getRealIp()
    {
        $ip = request()->header('CF-Connecting-IP') ?? request()->header('X-Forwarded-For') ?? request()->ip();
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        return $ip;
    }

    private static function getLokasi($ip)
    {
        if (preg_match('/(^127\.)|(^192\.168\.)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)/', $ip)) {
            return 'Jaringan Lokal / Tidak diketahui';
        }

        $response = Http::get("http://ip-api.com/json/{$ip}");
        if ($response->successful() && $response['status'] === 'success') {
            return "{$response['city']}, {$response['country']} ({$response['isp']})";
        }

        return 'Gagal dilacak oleh sistem';
    }
}