<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    // Dashboard Retur BS
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalHariIni = Retur::whereDate('tanggal_bon_in', $today)->count();
        $totalMingguIni = Retur::where('tanggal_bon_in', '>=', $startOfWeek)->count();
        $totalBulanIni = Retur::where('tanggal_bon_in', '>=', $startOfMonth)->count();

        $returTerbaru = Retur::with('items')->latest('tanggal_bon_in')->take(5)->get();

        // 3 SKU terbanyak di retur
        $skuTerbanyak = ReturItem::select('sku', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(sum_price) as total_harga'))
            ->groupBy('sku')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return view('retur.dashboard', compact('totalHariIni', 'totalMingguIni', 'totalBulanIni', 'returTerbaru', 'skuTerbanyak'));
    }

    // Form Input Retur
    public function create()
    {
        $produks = Produk::all();
        return view('retur.input', compact('produks'));
    }

    // Simpan Transaksi Retur
    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bon_in' => 'required|date',
            'nama_toko' => 'required|string',
            'driver' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        $retur = Retur::create([
            'tanggal_bon_in' => $request->tanggal_bon_in,
            'nama_toko' => $request->nama_toko,
            'driver' => $request->driver,
            'b2b' => $request->b2b,
            'nrb_outlet' => $request->nrb_outlet,
            'tanggal_bon' => $request->tanggal_bon,
            'keterangan' => $request->keterangan,
        ]);

        foreach ($request->items as $item) {
            ReturItem::create([
                'retur_id' => $retur->id,
                'sku' => $item['sku'],
                'qty' => $item['qty'],
                'status' => $item['status'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'sum_price' => $item['sum_price'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('retur.dashboard')->with('success', 'Data Retur BS berhasil disimpan.');
    }

    // History Level 1: Berdasarkan Bulan
    public function historyIndex()
    {
        $bulanList = Retur::select(DB::raw("strftime('%Y-%m', tanggal_bon_in) as bulan"), DB::raw('COUNT(*) as total_retur'))
            ->groupBy('bulan')
            ->orderByDesc('bulan')
            ->get();

        return view('retur.history_bulan', compact('bulanList'));
    }

    // History Level 2: Berdasarkan Tanggal pada Bulan tertentu
    public function historyByBulan($bulan)
    {
        $tanggalList = Retur::where(DB::raw("strftime('%Y-%m', tanggal_bon_in)"), $bulan)
            ->select('tanggal_bon_in', DB::raw('COUNT(DISTINCT nama_toko) as total_toko'), DB::raw('COUNT(*) as total_retur'))
            ->groupBy('tanggal_bon_in')
            ->orderByDesc('tanggal_bon_in')
            ->get();

        return view('retur.history_tanggal', compact('bulan', 'tanggalList'));
    }

    // History Level 3: Detail Retur pada Tanggal tertentu
    public function historyDetailTanggal(Request $request, $tanggal)
    {
        $query = Retur::with('items')->where('tanggal_bon_in', $tanggal);

        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_toko', 'like', '%' . $request->cari . '%');
        }

        $returs = $query->get();

        return view('retur.history_detail', compact('tanggal', 'returs'));
    }
}