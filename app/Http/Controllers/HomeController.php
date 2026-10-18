<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Retur;
use App\Models\Karung; // Sesuaikan dengan nama model tabel karung Anda jika ada
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $now = Carbon::now();

        // 1. Hitung Bulanan & Persentase Perbandingan dari Bulan Lalu
        $currentMonthCount = Retur::whereMonth('tanggal_bon_in', $now->month)
                                  ->whereYear('tanggal_bon_in', $now->year)
                                  ->count();

        $lastMonthCount = Retur::whereMonth('tanggal_bon_in', $now->copy()->subMonth()->month)
                               ->whereYear('tanggal_bon_in', $now->copy()->subMonth()->year)
                               ->count();

        if ($lastMonthCount > 0) {
            $percentChange = (($currentMonthCount - $lastMonthCount) / $lastMonthCount) * 100;
            $formattedPercent = (round($percentChange, 1) >= 0 ? '+' : '') . round($percentChange, 1) . '% dari bulan lalu';
        } else {
            $formattedPercent = $currentMonthCount > 0 ? '+100% dari bulan lalu' : '0% dari bulan lalu';
        }

        // 2. Hitung Mingguan & Harian
        $totalMingguIni = Retur::where('tanggal_bon_in', '>=', $startOfWeek)->count();
        $totalHariIni = Retur::whereDate('tanggal_bon_in', $today)->count();

        // 3. Total Karung (Mengambil dari model Karung jika ada, atau hitung 0)
        $totalKarung = class_exists(Karung::class) ? Karung::count() : 0;

        // 4. Total Master Produk (SKU) Asli dari Database
        $totalProduk = Produk::count();

        // 5. Tabel Retur Terbaru (Real-time dari database)
        $returTerbaru = Retur::with('items')->latest('tanggal_bon_in')->take(5)->get();

        return view('home', compact(
            'user',
            'currentMonthCount',
            'formattedPercent',
            'totalMingguIni',
            'totalHariIni',
            'totalKarung',
            'totalProduk',
            'returTerbaru'
        ));
    }
}