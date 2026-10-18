<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemusnahan;
use App\Models\Karung;

class PemusnahanController extends Controller
{
    public function index(Request $request)
    {
        $pemusnahans = Pemusnahan::with('karung.items.produk')->latest('tanggal_pengajuan')->get();
        return view('pemusnahan.index', compact('pemusnahans'));
    }

    public function input()
    {
        $karungs = Karung::with('items.produk')->latest('tanggal')->get();
        
        // Ambil ID karung yang sudah pernah diajukan pemusnahan
        $usedKarungIds = Pemusnahan::pluck('karung_id')->toArray();
        
        // Hanya tampilkan karung yang belum pernah diajukan
        $karungs = Karung::whereNotIn('id', $usedKarungIds)->with('items.produk')->latest('tanggal')->get();

        return view('pemusnahan.input', compact('karungs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'karung_ids' => 'required|array|min:1',
            'karung_ids.*' => 'exists:karungs,id',
            'tahap' => 'required|integer|min:1|max:4',
        ]);

        foreach ($request->karung_ids as $karungId) {
            Pemusnahan::create([
                'karung_id' => $karungId,
                'tahap' => $request->tahap,
                'tanggal_pengajuan' => date('Y-m-d'),
            ]);
        }

        return redirect()->route('pemusnahan.index')->with('success', 'Pengajuan pemusnahan karung berhasil disimpan.');
    }

    public function destroy($id)
    {
        $pemusnahan = Pemusnahan::findOrFail($id);
        $pemusnahan->delete();

        return redirect()->route('pemusnahan.index')->with('success', 'Pengajuan pemusnahan berhasil dibatalkan. Karung kembali tersedia.');
    }

    public function pdf(Request $request)
    {
        $tahap = $request->tahap;
        $query = Pemusnahan::with('karung.items.produk');

        if ($tahap) {
            $query->where('tahap', $tahap);
        }

        $pemusnahans = $query->latest('tanggal_pengajuan')->get();
        return view('pemusnahan.pdf', compact('pemusnahans', 'tahap'));
    }
}