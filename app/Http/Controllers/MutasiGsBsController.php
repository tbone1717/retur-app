<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class MutasiGsBsController extends Controller
{
    public function index()
    {
        $mutasis = DB::table('mutasi_gs_bs_headers as h')
            ->leftJoin('mutasi_gs_bs_items as i', 'h.nomor_mutasi', '=', 'i.nomor_mutasi')
            ->select(
                'h.nomor_mutasi',
                'h.tanggal',
                'h.depo',
                'h.minggu_ke',
                'h.keputusan_transaksi',
                DB::raw('SUM(i.jumlah_harga) as total_harga'),
                DB::raw('COUNT(i.id) as total_item')
            )
            ->groupBy('h.nomor_mutasi', 'h.tanggal', 'h.depo', 'h.minggu_ke', 'h.keputusan_transaksi')
            ->orderBy('h.id', 'desc')
            ->get();

        return view('mutasi_gs_bs.index', compact('mutasis'));
    }

    public function create()
    {
        $produk = DB::table('produks')->get();
        return view('mutasi_gs_bs.create', compact('produk'));
    }

    // Menampilkan Form Edit Mutasi
    public function edit($nomor_mutasi)
    {
        $nomor = urldecode($nomor_mutasi);
        $header = DB::table('mutasi_gs_bs_headers')->where('nomor_mutasi', $nomor)->first();
        if (!$header) {
            return redirect()->route('mutasi-gs-bs.index')->with('error', 'Data tidak ditemukan.');
        }
        $items = DB::table('mutasi_gs_bs_items')->where('nomor_mutasi', $nomor)->get();
        $produk = DB::table('produks')->get();

        return view('mutasi_gs_bs.edit', compact('header', 'items', 'produk'));
    }

    // Memperbarui Data Mutasi
    public function update(Request $request, $nomor_mutasi)
    {
        $nomor = urldecode($nomor_mutasi);

        $request->validate([
            'tanggal'       => 'required|date',
            'depo'          => 'required|string',
            'minggu_ke'     => 'required|string',
            'kode_produk'   => 'required|array',
            'kode_produk.*' => 'required|string',
        ]);

        $now = now();

        // Update Header
        DB::table('mutasi_gs_bs_headers')->where('nomor_mutasi', $nomor)->update([
            'tanggal'             => $request->tanggal,
            'depo'                => $request->depo,
            'region'              => $request->region ?? 'JABOTABEK',
            'gudang_asal'         => $request->gudang_asal ?? 'Gudang Bagus (GS)',
            'type_stock_asal'     => $request->type_stock_asal ?? 'GS',
            'gudang_tujuan'       => $request->gudang_tujuan ?? 'Bad Stock (BS)',
            'minggu_ke'           => $request->minggu_ke,
            'keputusan_transaksi' => $request->keputusan_transaksi ?? 'Mutasi Internal Depo',
            'updated_at'          => $now,
        ]);

        // Hapus items lama, insert yang baru
        DB::table('mutasi_gs_bs_items')->where('nomor_mutasi', $nomor)->delete();

        $items = [];
        $produks = DB::table('produks')->get()->keyBy(function($item) {
            return strtoupper(trim($item->sku));
        });

        foreach ($request->kode_produk as $index => $sku) {
            $skuClean = trim($sku);
            if (empty($skuClean)) continue;

            $qty_ed  = $request->qty_ed[$index] ?? 0;
            $qty_hm  = $request->qty_hm[$index] ?? 0;
            $qty_kr  = $request->qty_kr[$index] ?? 0;
            $qty_ns  = $request->qty_ns[$index] ?? 0;
            $qty_ll  = $request->qty_ll[$index] ?? 0;
            $qty_ned = $request->qty_ned[$index] ?? 0;

            $total_pcs = $qty_ed + $qty_hm + $qty_kr + $qty_ns + $qty_ll + $qty_ned;

            $product = $produks->get(strtoupper($skuClean));
            $hargaSatuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $jumlahHarga = $total_pcs * $hargaSatuan;

            $items[] = [
                'nomor_mutasi' => $nomor,
                'kode_produk'  => $skuClean,
                'qty_ed'       => $qty_ed,
                'qty_hm'       => $qty_hm,
                'qty_kr'       => $qty_kr,
                'qty_ns'       => $qty_ns,
                'qty_ll'       => $qty_ll,
                'qty_ned'      => $qty_ned,
                'total_pcs'    => $total_pcs,
                'jumlah_harga' => $jumlahHarga,
                'keterangan'   => $request->keterangan[$index] ?? null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (count($items) > 0) {
            DB::table('mutasi_gs_bs_items')->insert($items);
        }

        return redirect()->route('mutasi-gs-bs.index')->with('success', 'Data Mutasi berhasil diperbarui!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_mutasi'  => 'required|string|unique:mutasi_gs_bs_headers,nomor_mutasi',
            'tanggal'       => 'required|date',
            'depo'          => 'required|string',
            'minggu_ke'     => 'required|string',
            'kode_produk'   => 'required|array',
            'kode_produk.*' => 'required|string',
        ]);

        $now = now();
        DB::table('mutasi_gs_bs_headers')->insert([
            'nomor_mutasi'        => $request->nomor_mutasi,
            'tanggal'             => $request->tanggal,
            'depo'                => $request->depo,
            'region'              => $request->region ?? 'JABOTABEK',
            'gudang_asal'         => $request->gudang_asal ?? 'Gudang Bagus (GS)',
            'type_stock_asal'     => $request->type_stock_asal ?? 'GS',
            'gudang_tujuan'       => $request->gudang_tujuan ?? 'Bad Stock (BS)',
            'minggu_ke'           => $request->minggu_ke,
            'keputusan_transaksi' => $request->keputusan_transaksi ?? 'Mutasi Internal Depo',
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        $items = [];
        $produks = DB::table('produks')->get()->keyBy(function($item) {
            return strtoupper(trim($item->sku));
        });

        foreach ($request->kode_produk as $index => $sku) {
            $skuClean = trim($sku);
            if (empty($skuClean)) continue;

            $qty_ed  = $request->qty_ed[$index] ?? 0;
            $qty_hm  = $request->qty_hm[$index] ?? 0;
            $qty_kr  = $request->qty_kr[$index] ?? 0;
            $qty_ns  = $request->qty_ns[$index] ?? 0;
            $qty_ll  = $request->qty_ll[$index] ?? 0;
            $qty_ned = $request->qty_ned[$index] ?? 0;

            $total_pcs = $qty_ed + $qty_hm + $qty_kr + $qty_ns + $qty_ll + $qty_ned;

            $product = $produks->get(strtoupper($skuClean));
            $hargaSatuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $jumlahHarga = $total_pcs * $hargaSatuan;

            $items[] = [
                'nomor_mutasi' => $request->nomor_mutasi,
                'kode_produk'  => $skuClean,
                'qty_ed'       => $qty_ed,
                'qty_hm'       => $qty_hm,
                'qty_kr'       => $qty_kr,
                'qty_ns'       => $qty_ns,
                'qty_ll'       => $qty_ll,
                'qty_ned'      => $qty_ned,
                'total_pcs'    => $total_pcs,
                'jumlah_harga' => $jumlahHarga,
                'keterangan'   => $request->keterangan[$index] ?? null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        if (count($items) > 0) {
            DB::table('mutasi_gs_bs_items')->insert($items);
        }

        return redirect()->route('mutasi-gs-bs.index')->with('success', 'Mutasi GS to BS berhasil disimpan!');
    }

    public function show($nomor_mutasi)
    {
        $nomor = urldecode($nomor_mutasi);
        $header = DB::table('mutasi_gs_bs_headers')->where('nomor_mutasi', $nomor)->first();
        if (!$header) {
            return redirect()->route('mutasi-gs-bs.index')->with('error', 'Data tidak ditemukan.');
        }
        $items = DB::table('mutasi_gs_bs_items')->where('nomor_mutasi', $nomor)->get();

        return view('mutasi_gs_bs.show', compact('header', 'items'));
    }

    public function destroy($nomor_mutasi)
    {
        $nomor = urldecode($nomor_mutasi);
        DB::table('mutasi_gs_bs_headers')->where('nomor_mutasi', $nomor)->delete();
        DB::table('mutasi_gs_bs_items')->where('nomor_mutasi', $nomor)->delete();

        return redirect()->route('mutasi-gs-bs.index')->with('success', 'Data Mutasi berhasil dihapus!');
    }

    public function exportExcel($nomor_mutasi)
    {
        return $this->generateFile($nomor_mutasi, 'excel');
    }

    public function exportPdf($nomor_mutasi)
    {
        return $this->generateFile($nomor_mutasi, 'pdf');
    }

    private function generateFile($nomor_mutasi, $type)
    {
        $nomor = urldecode($nomor_mutasi);
        $header = DB::table('mutasi_gs_bs_headers')->where('nomor_mutasi', $nomor)->first();
        $items = DB::table('mutasi_gs_bs_items')->where('nomor_mutasi', $nomor)->get();

        if (!$header) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $templatePath = storage_path('app/templates/Mutasi GS to BS.xlsx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template Mutasi GS to BS.xlsx tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName('Mutasi GS-BS (Damage Rate)') ?? $spreadsheet->getActiveSheet();

        if ($type == 'pdf') {
            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setFitToPage(true);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(1);
            $sheet->setShowGridlines(false);
        }

        // Format Hari & Tanggal Indonesia
        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal ? date('D', strtotime($header->tanggal)) : '';
        $indoDay = $days[$engDay] ?? '';
        $formatTanggal = $indoDay . ' / ' . date('d-m-Y', strtotime($header->tanggal));

        // Header Dokumen
        $sheet->setCellValue('E5', ': ' . $header->nomor_mutasi);
        $sheet->setCellValue('E6', ': ' . $header->depo);
        $sheet->setCellValue('E7', ': ' . $header->region);
        $sheet->setCellValue('E8', ': ' . $formatTanggal);
        
        $sheet->setCellValue('L5', ': ' . $header->gudang_asal);
        $sheet->setCellValue('L6', ': ' . $header->type_stock_asal);
        $sheet->setCellValue('L7', ': ' . $header->gudang_tujuan);
        $sheet->setCellValue('L8', ': ' . $header->minggu_ke);

        // Keputusan Transaksi (Berikan tanda centang [ V ] pada pilihan yang sesuai)
        $pilihInternal = ($header->keputusan_transaksi == 'Dilakukan mutasi Internal Depo');
        $sheet->setCellValue('M6', ($pilihInternal ? '[ V ] ' : '[   ] ') . 'Dilakukan mutasi Internal Depo');
        $sheet->setCellValue('M7', (!$pilihInternal ? '[ V ] ' : '[   ] ') . 'Dilakukan Pembebanan');

        // Isi Item Produk mulai baris 13 hingga 32 (Field 0 dikosongkan)
        $rowIdx = 13;
        $grandTotalPcs = 0;
        $grandTotalHarga = 0;

        foreach ($items as $i => $item) {
            if ($rowIdx > 32) break;

            $qtyEd  = $item->qty_ed > 0 ? $item->qty_ed : '';
            $qtyHm  = $item->qty_hm > 0 ? $item->qty_hm : '';
            $qtyKr  = $item->qty_kr > 0 ? $item->qty_kr : '';
            $qtyNs  = $item->qty_ns > 0 ? $item->qty_ns : '';
            $qtyLl  = $item->qty_ll > 0 ? $item->qty_ll : '';
            $qtyNed = $item->qty_ned > 0 ? $item->qty_ned : '';
            
            $totalPcs = $item->total_pcs > 0 ? $item->total_pcs : '';
            $jumlahHarga = $item->jumlah_harga > 0 ? $item->jumlah_harga : '';

            $grandTotalPcs += ($item->total_pcs ?? 0);
            $grandTotalHarga += ($item->jumlah_harga ?? 0);

            $sheet->setCellValue('C' . $rowIdx, $i + 1);
            $sheet->setCellValue('D' . $rowIdx, $item->kode_produk);
            $sheet->setCellValue('E' . $rowIdx, $qtyEd);
            $sheet->setCellValue('F' . $rowIdx, $qtyHm);
            $sheet->setCellValue('G' . $rowIdx, $qtyKr);
            $sheet->setCellValue('H' . $rowIdx, $qtyNs);
            $sheet->setCellValue('I' . $rowIdx, $qtyLl);
            $sheet->setCellValue('J' . $rowIdx, $qtyNed);
            $sheet->setCellValue('K' . $rowIdx, $totalPcs);
            $sheet->setCellValue('L' . $rowIdx, $jumlahHarga);
            $sheet->setCellValue('M' . $rowIdx, $item->keterangan ?? '-');

            $rowIdx++;
        }

        // Kosongkan sisa baris hingga 32
        for ($r = $rowIdx; $r <= 32; $r++) {
            $sheet->setCellValue('C' . $r, '');
            $sheet->setCellValue('D' . $r, '');
            $sheet->setCellValue('E' . $r, '');
            $sheet->setCellValue('F' . $r, '');
            $sheet->setCellValue('G' . $r, '');
            $sheet->setCellValue('H' . $r, '');
            $sheet->setCellValue('I' . $r, '');
            $sheet->setCellValue('J' . $r, '');
            $sheet->setCellValue('K' . $r, '');
            $sheet->setCellValue('L' . $r, '');
            $sheet->setCellValue('M' . $r, '');
        }

        // Total Pengajuan (Baris 34)
        $sheet->setCellValue('K34', $grandTotalPcs > 0 ? $grandTotalPcs : '');
        $sheet->setCellValue('L34', $grandTotalHarga > 0 ? $grandTotalHarga : '');

        // Akumulasi Week Otomatis di tabel bawah
        $allMutasiBulanIni = DB::table('mutasi_gs_bs_headers as h')
            ->join('mutasi_gs_bs_items as i', 'h.nomor_mutasi', '=', 'i.nomor_mutasi')
            ->select('h.minggu_ke', DB::raw('SUM(i.jumlah_harga) as total_harga'))
            ->where('h.depo', $header->depo)
            ->groupBy('h.minggu_ke')
            ->get();

        $weekTotals = ['Week 1' => 0, 'Week 2' => 0, 'Week 3' => 0, 'Week 4' => 0];
        foreach ($allMutasiBulanIni as $row) {
            $wk = trim($row->minggu_ke);
            if (isset($weekTotals[$wk])) {
                $weekTotals[$wk] += $row->total_harga;
            }
        }
        $totalRekap = array_sum($weekTotals);

        $sheet->setCellValue('M38', $weekTotals['Week 1'] > 0 ? $weekTotals['Week 1'] : 0);
        $sheet->setCellValue('M39', $weekTotals['Week 2'] > 0 ? $weekTotals['Week 2'] : 0);
        $sheet->setCellValue('M40', $weekTotals['Week 3'] > 0 ? $weekTotals['Week 3'] : 0);
        $sheet->setCellValue('M41', $weekTotals['Week 4'] > 0 ? $weekTotals['Week 4'] : 0);
        $sheet->setCellValue('M42', $totalRekap > 0 ? $totalRekap : 0);

        $filename = 'Mutasi_GS_to_BS_' . str_replace(['/', '\\', ' '], '_', $nomor) . ($type == 'excel' ? '.xlsx' : '.pdf');

        $contentType = $type == 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf';
        $writerType = $type == 'excel' ? 'Xlsx' : 'Dompdf';

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, $writerType);
        $writer->save('php://output');
        exit;
    }
}