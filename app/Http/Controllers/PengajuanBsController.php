<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class PengajuanBsController extends Controller
{
    // Menampilkan Rekap Daftar Pengajuan (Digrup per Nomor Pengajuan)
    public function index()
    {
        $pengajuans = DB::table('pengajuan_bs_items')
            ->select(
                'nomor_pengajuan',
                DB::raw('MAX(tanggal_pengajuan) as tanggal_pengajuan'),
                DB::raw('MAX(periode) as periode'),
                DB::raw('MAX(depo_asal) as depo_asal'),
                DB::raw('MAX(jenis_pemusnahan) as jenis_pemusnahan'),
                DB::raw('MAX(status_produk) as status_produk'),
                DB::raw('SUM(jumlah_harga) as total_harga'),
                DB::raw('COUNT(id) as total_item')
            )
            ->whereNotNull('nomor_pengajuan')
            ->where('nomor_pengajuan', '!=', '')
            ->groupBy('nomor_pengajuan')
            ->orderBy('nomor_pengajuan', 'desc')
            ->get();

        return view('pengajuan_bs.index', compact('pengajuans'));
    }

    // Menampilkan Detail Pengajuan Ketika No. Pengajuan diklik
    public function show($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        $header = DB::table('pengajuan_bs_items')
            ->where('nomor_pengajuan', $nomor)
            ->first();

        if (!$header) {
            return redirect()->route('form.bs.index')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $items = DB::table('pengajuan_bs_items')
            ->where('nomor_pengajuan', $nomor)
            ->get();

        return view('pengajuan_bs.show', compact('header', 'items'));
    }

    // Form Input Batch
    public function create()
    {
        $produk = DB::table('produks')->get();
        
        $statuses = [];
        $kolomStatus = null;
        foreach (['status', 'kategori', 'brand', 'group', 'jenis'] as $col) {
            if (Schema::hasColumn('produks', $col)) {
                $kolomStatus = $col;
                break;
            }
        }

        if ($kolomStatus) {
            $statuses = DB::table('produks')
                ->select($kolomStatus)
                ->whereNotNull($kolomStatus)
                ->where($kolomStatus, '!=', '')
                ->distinct()
                ->pluck($kolomStatus);
        }

        return view('pengajuan_bs.create', compact('produk', 'statuses'));
    }

    // Edit Form
    public function edit($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        $header = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->first();
        $items = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->get();
        $produks = DB::table('produks')->get();

        if (!$header) {
            return redirect()->route('form.bs.index')->with('error', 'Data pengajuan tidak ditemukan.');
        }

        $statuses = [];
        $kolomStatus = null;
        foreach (['status', 'kategori', 'brand', 'group', 'jenis'] as $col) {
            if (Schema::hasColumn('produks', $col)) {
                $kolomStatus = $col;
                break;
            }
        }

        if ($kolomStatus) {
            $statuses = DB::table('produks')
                ->select($kolomStatus)
                ->whereNotNull($kolomStatus)
                ->where($kolomStatus, '!=', '')
                ->distinct()
                ->pluck($kolomStatus);
        }

        // Tambahkan variabel $statuses ke fungsi compact()
        return view('pengajuan_bs.edit', compact('header', 'items', 'produks', 'statuses'));
    }

    // Update Data Pengajuan
    public function update(Request $request, $nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);

        $request->validate([
            'nomor_pengajuan'   => 'required|string',
            'tanggal_pengajuan' => 'required|date',
            'periode'           => 'required|string',
            'depo_asal'         => 'required|string',
            'jenis_pemusnahan'  => 'required|string',
            'status_produk'     => 'required|string',
            'kode_produk'       => 'required|array',
            'kode_produk.*'     => 'required|string',
        ]);

        // Hapus data lama berdasarkan nomor pengajuan
        DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->delete();

        $items = [];
        $now = now();
        $produks = DB::table('produks')->get()->keyBy('sku');

        foreach ($request->kode_produk as $index => $sku) {
            if (empty($sku)) continue;

            $qty_ed  = $request->qty_ed[$index] ?? 0;
            $qty_hm  = $request->qty_hm[$index] ?? 0;
            $qty_kr  = $request->qty_kr[$index] ?? 0;
            $qty_ns  = $request->qty_ns[$index] ?? 0;
            $qty_ned = $request->qty_ned[$index] ?? 0;

            $total_pcs = $qty_ed + $qty_hm + $qty_kr + $qty_ns + $qty_ned;
            
            $product = $produks->get($sku);
            $konversiUom = $product ? ($product->uom ?? 1) : 1;
            if ($konversiUom <= 0) $konversiUom = 1;

            $total_uom = round($total_pcs / $konversiUom);

            $harga_satuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $jumlah_harga = $total_pcs * $harga_satuan;

            $items[] = [
                'nomor_pengajuan'    => $request->nomor_pengajuan,
                'tanggal_pengajuan'  => $request->tanggal_pengajuan,
                'periode'            => $request->periode,
                'depo_asal'          => $request->depo_asal,
                'jenis_pemusnahan'   => $request->jenis_pemusnahan,
                'status_produk'      => $request->status_produk,
                'kode_produk'        => $sku,
                'qty_ed'             => $qty_ed,
                'qty_hm'             => $qty_hm,
                'qty_kr'             => $qty_kr,
                'qty_ns'             => $qty_ns,
                'qty_ned'            => $qty_ned,
                'total_pcs'          => $total_pcs,
                'total_uom'          => $total_uom,
                'jumlah_harga'       => $jumlah_harga,
                'keterangan_proling' => $request->keterangan_proling[$index] ?? null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        if (count($items) > 0) {
            DB::table('pengajuan_bs_items')->insert($items);
        }

        return redirect()->route('form.bs.index')->with('success', 'Data pengajuan BS berhasil diperbarui!');
    }

    // Simpan Data Batch Baru
    public function store(Request $request)
    {
        $request->validate([
            'nomor_pengajuan'   => 'required|string',
            'tanggal_pengajuan' => 'required|date',
            'periode'           => 'required|string',
            'depo_asal'         => 'required|string',
            'jenis_pemusnahan'  => 'required|string',
            'status_produk'     => 'required|string',
            'kode_produk'       => 'required|array',
            'kode_produk.*'     => 'required|string',
        ]);

        $items = [];
        $now = now();
        $produks = DB::table('produks')->get()->keyBy('sku');

        foreach ($request->kode_produk as $index => $sku) {
            if (empty($sku)) continue;

            $qty_ed  = $request->qty_ed[$index] ?? 0;
            $qty_hm  = $request->qty_hm[$index] ?? 0;
            $qty_kr  = $request->qty_kr[$index] ?? 0;
            $qty_ns  = $request->qty_ns[$index] ?? 0;
            $qty_ned = $request->qty_ned[$index] ?? 0;

            $total_pcs = $qty_ed + $qty_hm + $qty_kr + $qty_ns + $qty_ned;
            
            $product = $produks->get($sku);
            $konversiUom = $product ? ($product->uom ?? 1) : 1;
            if ($konversiUom <= 0) $konversiUom = 1;

            $total_uom = round($total_pcs / $konversiUom);

            $harga_satuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $jumlah_harga = $total_pcs * $harga_satuan;

            $items[] = [
                'nomor_pengajuan'    => $request->nomor_pengajuan,
                'tanggal_pengajuan'  => $request->tanggal_pengajuan,
                'periode'            => $request->periode,
                'depo_asal'          => $request->depo_asal,
                'jenis_pemusnahan'   => $request->jenis_pemusnahan,
                'status_produk'      => $request->status_produk,
                'kode_produk'        => $sku,
                'qty_ed'             => $qty_ed,
                'qty_hm'             => $qty_hm,
                'qty_kr'             => $qty_kr,
                'qty_ns'             => $qty_ns,
                'qty_ned'            => $qty_ned,
                'total_pcs'          => $total_pcs,
                'total_uom'          => $total_uom,
                'jumlah_harga'       => $jumlah_harga,
                'keterangan_proling' => $request->keterangan_proling[$index] ?? null,
                'created_at'         => $now,
                'updated_at'         => $now,
            ];
        }

        if (count($items) > 0) {
            DB::table('pengajuan_bs_items')->insert($items);
        }

        return redirect()->route('form.bs.index')->with('success', count($items) . ' baris pengajuan BS berhasil disimpan!');
    }

    // Hapus Data Pengajuan
    public function destroy($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->delete();

        return redirect()->route('form.bs.index')->with('success', 'Data pengajuan BS berhasil dihapus!');
    }

    // Export Excel menggunakan Master Template
    public function exportExcel($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        $header = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->first();
        $items = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->get();

        if (!$header) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $produks = DB::table('produks')->get()->keyBy('sku');
        $templatePath = storage_path('app/templates/template_pemusnahan.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File master template.xlsx tidak ditemukan di storage/app/templates/');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName('Pengajuan BS');

        // Format Hari & Tanggal Bahasa Indonesia
        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal_pengajuan ? date('D', strtotime($header->tanggal_pengajuan)) : '';
        $indoDay = $days[$engDay] ?? '';
        $hariTanggal = $header->tanggal_pengajuan ? $indoDay . ' / ' . date('d-m-Y', strtotime($header->tanggal_pengajuan)) : '-';

        // 1. Header Dokumen
        $sheet->setCellValue('E5', ' : ' . $header->nomor_pengajuan);
        $sheet->setCellValue('E6', ' : ' . $header->depo_asal);
        $sheet->setCellValue('E7', ' : Jabobeka');
        $sheet->setCellValue('E8', ' : ' . $hariTanggal);
        $sheet->setCellValue('E9', ' : ' . $header->periode);
        $sheet->setCellValue('C10', 'Jenis Pemusnahan  : ' . $header->jenis_pemusnahan);

        // 2. Isi data items mulai dari baris 17
        $rowIdx = 17;
        $totalHargaKeseluruhan = 0;

        foreach ($items as $i => $item) {
            if ($rowIdx > 129) break;

            $product = $produks->get($item->kode_produk);
            $hargaSatuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $konversiUom = $product ? ($product->uom ?? $product->konversi ?? 1) : 1;
            if ($konversiUom <= 0) $konversiUom = 1;

            $qtyEd  = $item->qty_ed ?? 0;
            $qtyHm  = $item->qty_hm ?? 0;
            $qtyKr  = $item->qty_kr ?? 0;
            $qtyNs  = $item->qty_ns ?? 0;
            $qtyNed = $item->qty_ned ?? 0;

            $totalPcs = $qtyEd + $qtyHm + $qtyKr + $qtyNs + $qtyNed;
            $totalUom = $totalPcs / $konversiUom;
            $jumlahHarga = $totalPcs * $hargaSatuan;
            $totalHargaKeseluruhan += $jumlahHarga;

            $sheet->setCellValue('C' . $rowIdx, $i + 1);
            $sheet->setCellValue('D' . $rowIdx, $item->kode_produk);
            
            $sheet->setCellValue('E' . $rowIdx, $qtyEd > 0 ? $qtyEd : '');
            $sheet->setCellValue('F' . $rowIdx, $qtyHm > 0 ? $qtyHm : '');
            $sheet->setCellValue('G' . $rowIdx, $qtyKr > 0 ? $qtyKr : '');
            $sheet->setCellValue('H' . $rowIdx, $qtyNs > 0 ? $qtyNs : '');
            $sheet->setCellValue('I' . $rowIdx, $qtyNed > 0 ? $qtyNed : '');
            
            $sheet->setCellValue('J' . $rowIdx, $totalPcs > 0 ? $totalPcs : '');
            $sheet->setCellValue('K' . $rowIdx, $totalUom > 0 ? $totalUom : '');
            $sheet->setCellValue('L' . $rowIdx, $jumlahHarga > 0 ? $jumlahHarga : '');
            $sheet->setCellValue('M' . $rowIdx, $item->keterangan_proling ?? '-');

            $sheet->getStyle('C' . $rowIdx . ':K' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('M' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getRowDimension($rowIdx)->setVisible(true);
            $rowIdx++;
        }

        // 3. Sembunyikan sisa baris kosong bersih tanpa tanda '-'
        for ($r = $rowIdx; $r <= 129; $r++) {
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

            $sheet->getRowDimension($r)->setVisible(false);
        }

        // 4. Akumulasi Week Otomatis dari Database
        $allPengajuanBulanIni = DB::table('pengajuan_bs_items')
            ->select('periode', 'nomor_pengajuan', DB::raw('SUM(jumlah_harga) as total_harga'))
            ->where('depo_asal', $header->depo_asal)
            ->groupBy('nomor_pengajuan', 'periode')
            ->get();

        $weekTotals = ['week 1' => 0, 'week 2' => 0, 'week 3' => 0, 'week 4' => 0, 'week 5' => 0];

        foreach ($allPengajuanBulanIni as $row) {
            $p = strtolower(trim($row->periode));
            foreach ($weekTotals as $wk => $val) {
                if (str_contains($p, $wk)) {
                    $weekTotals[$wk] += $row->total_harga;
                }
            }
        }

        $currentWeekKey = null;
        $currentPeriodeLower = strtolower(trim($header->periode));
        foreach (['week 1', 'week 2', 'week 3', 'week 4', 'week 5'] as $wk) {
            if (str_contains($currentPeriodeLower, $wk)) {
                $currentWeekKey = $wk;
                break;
            }
        }

        if ($currentWeekKey && $weekTotals[$currentWeekKey] == 0) {
            $weekTotals[$currentWeekKey] = $totalHargaKeseluruhan;
        }

        $sheet->setCellValue('K133', $weekTotals['week 1'] > 0 ? $weekTotals['week 1'] : 0);
        $sheet->setCellValue('K134', $weekTotals['week 2'] > 0 ? $weekTotals['week 2'] : 0);
        $sheet->setCellValue('K135', $weekTotals['week 3'] > 0 ? $weekTotals['week 3'] : 0);
        $sheet->setCellValue('K136', $weekTotals['week 4'] > 0 ? $weekTotals['week 4'] : 0);
        $sheet->setCellValue('K137', $weekTotals['week 5'] > 0 ? $weekTotals['week 5'] : 0);

        $filename = 'Form_Pengajuan_BS_' . str_replace(['/', '\\', ' '], '_', $nomor) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // Export PDF langsung dari Master Template (100% Identik dengan Excel)
    public function exportPdf($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        $header = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->first();
        $items = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->get();

        if (!$header) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $produks = DB::table('produks')->get()->keyBy('sku');
        $templatePath = storage_path('app/templates/template_pemusnahan.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File master template.xlsx tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName('Pengajuan BS');

        // Pengaturan Halaman Cetak PDF (Fit to 1 Page Landscape)
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
        $sheet->setShowGridlines(false);

        // Format Hari & Tanggal Bahasa Indonesia
        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal_pengajuan ? date('D', strtotime($header->tanggal_pengajuan)) : '';
        $indoDay = $days[$engDay] ?? '';
        $hariTanggal = $header->tanggal_pengajuan ? $indoDay . ' / ' . date('d-m-Y', strtotime($header->tanggal_pengajuan)) : '-';

        // 1. Isi Header Dokumen
        $sheet->setCellValue('E5', ' : ' . $header->nomor_pengajuan);
        $sheet->setCellValue('E6', ' : ' . $header->depo_asal);
        $sheet->setCellValue('E7', ' : Jabobeka');
        $sheet->setCellValue('E8', ' : ' . $hariTanggal);
        $sheet->setCellValue('E9', ' : ' . $header->periode);
        $sheet->setCellValue('C10', 'Jenis Pemusnahan  : ' . $header->jenis_pemusnahan);

        // 2. Isi Data Items mulai baris 17
        $rowIdx = 17;
        $totalHargaKeseluruhan = 0;

        foreach ($items as $i => $item) {
            if ($rowIdx > 129) break;

            $product = $produks->get($item->kode_produk);
            $hargaSatuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;
            $konversiUom = $product ? ($product->uom ?? $product->konversi ?? 1) : 1;
            if ($konversiUom <= 0) $konversiUom = 1;

            $qtyEd  = $item->qty_ed ?? 0;
            $qtyHm  = $item->qty_hm ?? 0;
            $qtyKr  = $item->qty_kr ?? 0;
            $qtyNs  = $item->qty_ns ?? 0;
            $qtyNed = $item->qty_ned ?? 0;

            $totalPcs = $qtyEd + $qtyHm + $qtyKr + $qtyNs + $qtyNed;
            $totalUom = $totalPcs / $konversiUom;
            $jumlahHarga = $totalPcs * $hargaSatuan;
            $totalHargaKeseluruhan += $jumlahHarga;

            $sheet->setCellValue('C' . $rowIdx, $i + 1);
            $sheet->setCellValue('D' . $rowIdx, $item->kode_produk);
            
            $sheet->setCellValue('E' . $rowIdx, $qtyEd > 0 ? $qtyEd : '');
            $sheet->setCellValue('F' . $rowIdx, $qtyHm > 0 ? $qtyHm : '');
            $sheet->setCellValue('G' . $rowIdx, $qtyKr > 0 ? $qtyKr : '');
            $sheet->setCellValue('H' . $rowIdx, $qtyNs > 0 ? $qtyNs : '');
            $sheet->setCellValue('I' . $rowIdx, $qtyNed > 0 ? $qtyNed : '');
            
            $sheet->setCellValue('J' . $rowIdx, $totalPcs > 0 ? $totalPcs : '');
            $sheet->setCellValue('K' . $rowIdx, $totalUom > 0 ? $totalUom : '');
            $sheet->setCellValue('L' . $rowIdx, $jumlahHarga > 0 ? $jumlahHarga : '');
            $sheet->setCellValue('M' . $rowIdx, $item->keterangan_proling ?? '-');

            $sheet->getStyle('C' . $rowIdx . ':K' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->getRowDimension($rowIdx)->setVisible(true);
            $rowIdx++;
        }

        // Sembunyikan sisa baris kosong agar PDF pas 1 halaman penuh
        for ($r = $rowIdx; $r <= 129; $r++) {
            $sheet->getRowDimension($r)->setVisible(false);
        }

        // 3. Akumulasi Week Otomatis
        $allPengajuanBulanIni = DB::table('pengajuan_bs_items')
            ->select('periode', 'nomor_pengajuan', DB::raw('SUM(jumlah_harga) as total_harga'))
            ->where('depo_asal', $header->depo_asal)
            ->groupBy('nomor_pengajuan', 'periode')
            ->get();

        $weekTotals = ['week 1' => 0, 'week 2' => 0, 'week 3' => 0, 'week 4' => 0, 'week 5' => 0];
        foreach ($allPengajuanBulanIni as $row) {
            $p = strtolower(trim($row->periode));
            foreach ($weekTotals as $wk => $val) {
                if (str_contains($p, $wk)) {
                    $weekTotals[$wk] += $row->total_harga;
                }
            }
        }

        $currentWeekKey = null;
        $currentPeriodeLower = strtolower(trim($header->periode));
        foreach (['week 1', 'week 2', 'week 3', 'week 4', 'week 5'] as $wk) {
            if (str_contains($currentPeriodeLower, $wk)) {
                $currentWeekKey = $wk;
                break;
            }
        }

        if ($currentWeekKey && $weekTotals[$currentWeekKey] == 0) {
            $weekTotals[$currentWeekKey] = $totalHargaKeseluruhan;
        }

        $sheet->setCellValue('K133', $weekTotals['week 1'] > 0 ? $weekTotals['week 1'] : 0);
        $sheet->setCellValue('K134', $weekTotals['week 2'] > 0 ? $weekTotals['week 2'] : 0);
        $sheet->setCellValue('K135', $weekTotals['week 3'] > 0 ? $weekTotals['week 3'] : 0);
        $sheet->setCellValue('K136', $weekTotals['week 4'] > 0 ? $weekTotals['week 4'] : 0);
        $sheet->setCellValue('K137', $weekTotals['week 5'] > 0 ? $weekTotals['week 5'] : 0);

        $filename = 'Form_Pengajuan_BS_' . str_replace(['/', '\\', ' '], '_', $nomor) . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Dompdf');
        $writer->save('php://output');
        exit;
    }

}