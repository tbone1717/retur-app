<?php
namespace App\Observers;

use App\Models\Retur;
use Illuminate\Support\Facades\Http;

class ReturObserver
{
    public function created(Retur $retur)
    {
        $user = auth()->user()?->name ?? 'Sistem';
        $this->sendTelegram("📦 *INPUT RETUR BARU*\n\n👤 Oleh: {$user}\n🏪 Toko: {$retur->nama_toko}\n🚚 Driver: {$retur->driver}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
    }

    public function deleted(Retur $retur)
    {
        $user = auth()->user()?->name ?? 'Sistem';
        $this->sendTelegram("🗑️ *DATA RETUR DIHAPUS*\n\n👤 Oleh: {$user}\n🏪 Toko: {$retur->nama_toko}\n⏰ Waktu: " . now()->format('d-m-Y H:i:s'));
    }

    private function sendTelegram($pesan)
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
}