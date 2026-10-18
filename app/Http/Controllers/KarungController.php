<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karung;
use App\Models\KarungItem;
use App\Models\Produk;
use App\Models\Pemusnahan;

class KarungController extends Controller
{
    public function index(Request $request)
    {
        $query = Karung::with('items.produk', 'pemusnahan');

        // Logika pencarian berdasarkan no karung, jenis produk, atau status
        if ($request->has('cari') && $request->cari != '') {
            $cari = $request->cari;
            $query->where(function($q) use ($cari) {
                $q->where('no_karung', 'like', '%' . $cari . '%')
                  ->orWhere('jenis_produk', 'like', '%' . $cari . '%')
                  ->orWhere('status', 'like', '%' . $cari . '%');
            });
        }

        $karungs = $query->latest('tanggal')->get();
        return view('karung.index', compact('karungs'));
    }

    public function input()
    {
        $produks = Produk::all();
        
        // Menghitung nomor urut karung untuk bulan ini (Reset ke 01 setiap bulan baru)
        $nextUrut = Karung::whereMonth('tanggal', date('m'))
                          ->whereYear('tanggal', date('Y'))
                          ->count() + 1;

        return view('karung.input', compact('produks', 'nextUrut'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_karung' => 'required|string|unique:karungs,no_karung',
            'tanggal' => 'required|date',
            'jenis_produk' => 'required|string',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        $karung = Karung::create([
            'no_karung' => $request->no_karung,
            'tanggal' => $request->tanggal,
            'jenis_produk' => $request->jenis_produk,
            'status' => $request->status,
        ]);

        foreach ($request->items as $item) {
            if (empty($item['sku'])) continue;

            KarungItem::create([
                'karung_id' => $karung->id,
                'sku' => $item['sku'],
                'qty' => $item['qty'] ?? 1,
            ]);
        }

        return redirect()->route('karung.index')->with('success', 'Data Karung berhasil disimpan.');
    }

    public function edit($id)
    {
        // Proteksi: Jika karung sudah diajukan pemusnahan, tolak akses edit
        if (Pemusnahan::where('karung_id', $id)->exists()) {
            return redirect()->route('karung.index')->with('error', 'Karung yang sudah diajukan pemusnahan tidak dapat diedit. Batalkan dulu pengajuan pemusnahannya.');
        }
        
        $karung = Karung::with('items')->findOrFail($id);
        $produks = Produk::all();
        return view('karung.edit', compact('karung', 'produks'));
    }

    public function update(Request $request, $id)
    {
        // Proteksi tambahan di update method
        if (Pemusnahan::where('karung_id', $id)->exists()) {
            return redirect()->route('karung.index')->with('error', 'Karung yang sudah diajukan pemusnahan tidak dapat diubah.');
        }

        $request->validate([
            'no_karung' => 'required|string|unique:karungs,no_karung,' . $id,
            'tanggal' => 'required|date',
            'jenis_produk' => 'required|string',
            'status' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        $karung = Karung::findOrFail($id);
        $karung->update([
            'no_karung' => $request->no_karung,
            'tanggal' => $request->tanggal,
            'jenis_produk' => $request->jenis_produk,
            'status' => $request->status,
        ]);

        // Reset item lama dan simpan yang baru
        $karung->items()->delete();

        foreach ($request->items as $item) {
            if (empty($item['sku'])) continue;

            KarungItem::create([
                'karung_id' => $karung->id,
                'sku' => $item['sku'],
                'qty' => $item['qty'] ?? 1,
            ]);
        }

        return redirect()->route('karung.index')->with('success', 'Data Karung berhasil diperbarui.');
    }
}