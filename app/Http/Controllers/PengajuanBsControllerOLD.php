<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Dompdf as PdfWriter; // atau Mpdf

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

    // Edit
    public function edit($nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);
        $header = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->first();
        $items = DB::table('pengajuan_bs_items')->where('nomor_pengajuan', $nomor)->get();
        $produks = DB::table('produks')->get();

        if (!$header) {
            return redirect()->route('form.bs.index')->with('error', 'Data tidak ditemukan.');
        }

        return view('pengajuan_bs.edit', compact('header', 'items', 'produks'));
    }

    // Update
    public function update(Request $request, $nomor_pengajuan)
    {
        $nomor = urldecode($nomor_pengajuan);

        // Logika update item pengajuan BS sesuai kebutuhan Anda
        // (biasanya hapus item lama berdasarkan nomor pengajuan lalu insert ulang item baru dari form)

        return redirect()->route('form.bs.index')->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    // Simpan Data Batch
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
            $input_uom = (isset($request->input_uom[$index]) && $request->input_uom[$index] > 0) ? $request->input_uom[$index] : 1;
            $total_uom = (int) round($total_pcs / $input_uom);

            $product = $produks->get($sku);
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

            // Ambil harga retail produk
            $product = $produks->get($item->kode_produk);
            $hargaSatuan = $product ? ($product->harga ?? $product->price ?? $product->hargajual ?? 0) : 0;

            // Ambil konversi uom (isi per karton) dari master produk
            $konversiUom = $product ? ($product->isi_per_karton ?? $product->konversi ?? 1) : 1;
            if ($konversiUom <= 0) $konversiUom = 1;

            $qtyEd  = $item->qty_ed ?? 0;
            $qtyHm  = $item->qty_hm ?? 0;
            $qtyKr  = $item->qty_kr ?? 0;
            $qtyNs  = $item->qty_ns ?? 0;
            $qtyNed = $item->qty_ned ?? 0;

            $totalPcs = $qtyEd + $qtyHm + $qtyKr + $qtyNs + $qtyNed;
            
            // Hitung In UoM yang benar (Total PCS dibagi konversi isi per karton)
            $totalUom = $totalPcs / $konversiUom; 
            
            $jumlahHarga = $totalPcs * $hargaSatuan;
            $totalHargaKeseluruhan += $jumlahHarga;

            // Isi nilai sel
            $sheet->setCellValue('C' . $rowIdx, $i + 1);
            $sheet->setCellValue('D' . $rowIdx, $item->kode_produk);
            
            // Permintaan 2: Jika 0 dikosongkan (tanpa '-')
            $sheet->setCellValue('E' . $rowIdx, $qtyEd > 0 ? $qtyEd : '');
            $sheet->setCellValue('F' . $rowIdx, $qtyHm > 0 ? $qtyHm : '');
            $sheet->setCellValue('G' . $rowIdx, $qtyKr > 0 ? $qtyKr : '');
            $sheet->setCellValue('H' . $rowIdx, $qtyNs > 0 ? $qtyNs : '');
            $sheet->setCellValue('I' . $rowIdx, $qtyNed > 0 ? $qtyNed : '');
            
            $sheet->setCellValue('J' . $rowIdx, $totalPcs > 0 ? $totalPcs : '');
            $sheet->setCellValue('K' . $rowIdx, $totalUom > 0 ? $totalUom : '');
            $sheet->setCellValue('L' . $rowIdx, $jumlahHarga > 0 ? $jumlahHarga : '');
            $sheet->setCellValue('P' . $rowIdx, $item->keterangan_proling ?? '-');

            // Alignment Center
            $sheet->getStyle('C' . $rowIdx . ':K' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $rowIdx++;
        }

        // 3. Sisa baris kosong dibiarkan kosong bersih (tanpa garis strip '-')
        for ($r = $rowIdx; $r <= 129; $r++) {
            $sheet->setCellValue('C' . $r, $r - 16);
            $sheet->setCellValue('D' . $r, '');
            $sheet->setCellValue('E' . $r, '');
            $sheet->setCellValue('F' . $r, '');
            $sheet->setCellValue('G' . $r, '');
            $sheet->setCellValue('H' . $r, '');
            $sheet->setCellValue('I' . $r, '');
            $sheet->setCellValue('J' . $r, '');
            $sheet->setCellValue('K' . $r, '');
            $sheet->setCellValue('L' . $r, '');
            $sheet->setCellValue('P' . $r, '');

            // Sembunyikan baris kosong secara otomatis agar tidak tampil penuh angka 0
            $sheet->getRowDimension($r)->setVisible(false);
        }


        // 4. Permintaan 3: Akumulasi Pemusnahan otomatis mengambil data dari database berdasarkan Week
        // Ambil semua pengajuan dengan depo dan periode/bulan yang sama dari database
        $allPengajuanBulanIni = DB::table('pengajuan_bs_items')
            ->select('periode', 'nomor_pengajuan', DB::raw('SUM(jumlah_harga) as total_harga'))
            ->where('depo_asal', $header->depo_asal)
            ->groupBy('nomor_pengajuan', 'periode')
            ->get();

        // Hitung total harga per Week secara otomatis dari database
        $weekTotals = ['week 1' => 0, 'week 2' => 0, 'week 3' => 0, 'week 4' => 0, 'week 5' => 0];

        foreach ($allPengajuanBulanIni as $row) {
            $p = strtolower(trim($row->periode));
            // Deteksi Week 1 / Angka 1
            if (str_contains($p, 'week 1') || preg_match('/\b1\b/', $p) || str_contains($p, 'Satu')) {
                $weekTotals['week 1'] += $row->total_harga;
            }
            // Deteksi Week 2 / Angka 2
            elseif (str_contains($p, 'week 2') || preg_match('/\b2\b/', $p) || str_contains($p, 'Dua')) {
                $weekTotals['week 2'] += $row->total_harga;
            }
            // Deteksi Week 3 / Angka 3
            elseif (str_contains($p, 'week 3') || preg_match('/\b3\b/', $p) || str_contains($p, 'Tiga')) {
                $weekTotals['week 3'] += $row->total_harga;
            }
            // Deteksi Week 4 / Angka 4
            elseif (str_contains($p, 'week 4') || preg_match('/\b4\b/', $p) || str_contains($p, 'Empat')) {
                $weekTotals['week 4'] += $row->total_harga;
            }
            // Deteksi Week 5 / Angka 5
            elseif (str_contains($p, 'week 5') || preg_match('/\b5\b/', $p) || str_contains($p, 'Lima')) {
                $weekTotals['week 5'] += $row->total_harga;
            }
        }

        // Pastikan pengajuan saat ini juga masuk jika belum terhitung
        $currentWeekKey = null;
        $currentPeriodeLower = strtolower(trim($header->periode));

        if (str_contains($currentPeriodeLower, 'week 1') || preg_match('/\b1\b/', $currentPeriodeLower) || str_contains($currentPeriodeLower, 'Satu')) {
            $currentWeekKey = 'week 1';
        } elseif (str_contains($currentPeriodeLower, 'week 2') || preg_match('/\b2\b/', $currentPeriodeLower) || str_contains($currentPeriodeLower, 'Dua')) {
            $currentWeekKey = 'week 2';
        } elseif (str_contains($currentPeriodeLower, 'week 3') || preg_match('/\b3\b/', $currentPeriodeLower) || str_contains($currentPeriodeLower, 'Tiga')) {
            $currentWeekKey = 'week 3';
        } elseif (str_contains($currentPeriodeLower, 'week 4') || preg_match('/\b4\b/', $currentPeriodeLower) || str_contains($currentPeriodeLower, 'Empat')) {
            $currentWeekKey = 'week 4';
        } elseif (str_contains($currentPeriodeLower, 'week 5') || preg_match('/\b5\b/', $currentPeriodeLower) || str_contains($currentPeriodeLower, 'Lima')) {
            $currentWeekKey = 'week 5';
        }

        if ($currentWeekKey && $weekTotals[$currentWeekKey] == 0) {
            $weekTotals[$currentWeekKey] = $totalHargaKeseluruhan;
        }

        // Masukkan ke sel Excel Akumulasi (Baris 91 s.d. 95, Kolom K)
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

    // Download PDF Format Resmi Sesuai Excel
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

        // 1. Load Spreadsheet master template
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getSheetByName('Pengajuan BS');

        // Format Hari & Tanggal Bahasa Indonesia
        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal_pengajuan ? date('D', strtotime($header->tanggal_pengajuan)) : '';
        $indoDay = $days[$engDay] ?? '';
        $hariTanggal = $header->tanggal_pengajuan ? $indoDay . ' / ' . date('d-m-Y', strtotime($header->tanggal_pengajuan)) : '-';

        // 2. Isi Header Dokumen
        $sheet->setCellValue('E5', ' : ' . $header->nomor_pengajuan);
        $sheet->setCellValue('E6', ' : ' . $header->depo_asal);
        $sheet->setCellValue('E7', ' : Jabobeka');
        $sheet->setCellValue('E8', ' : ' . $hariTanggal);
        $sheet->setCellValue('E9', ' : ' . $header->periode);
        $sheet->setCellValue('C10', 'Jenis Pemusnahan  : ' . $header->jenis_pemusnahan);

        // 3. Isi Data Items mulai baris 17
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
            $sheet->setCellValue('K' . $rowIdx, $totalUom > 0 ? $totalUom : '4');
            $sheet->setCellValue('L' . $rowIdx, $jumlahHarga > 0 ? $jumlahHarga : '');
            $sheet->setCellValue('P' . $rowIdx, $item->keterangan_proling ?? '-');

            $sheet->getStyle('C' . $rowIdx . ':K' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L' . $rowIdx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Sembunyikan baris yang tidak ada isinya agar halaman PDF tidak melebar ke bawah
            $sheet->getRowDimension($rowIdx)->setVisible(true);

            $rowIdx++;
        }

        // Kosongkan sisa baris
        // Sembunyikan sisa baris kosong agar PDF pas 1 halaman penuh
        for ($r = $rowIdx; $r <= 129; $r++) {
            $sheet->getRowDimension($r)->setVisible(false);
        }

        // 4. Akumulasi Week Otomatis
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

        // Set Header untuk Output PDF langsung dari Spreadsheet
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Gunakan Dompdf writer bawaan PhpSpreadsheet agar layout 100% identik dengan Excel
        $writer = IOFactory::createWriter($spreadsheet, 'Dompdf');
        $writer->save('php://output');
        exit;
    }
}