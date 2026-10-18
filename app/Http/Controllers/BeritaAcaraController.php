<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class BeritaAcaraController extends Controller
{
    // Menampilkan Daftar Berita Acara
    public function index()
    {
        $baps = DB::table('berita_acara')->orderBy('id', 'desc')->get();
        return view('berita_acara.index', compact('baps'));
    }

    // Form Input BAP Baru
    public function create()
    {
        return view('berita_acara.create');
    }

    // Simpan Data BAP Baru
    public function store(Request $request)
    {
        $request->validate([
            'nomor_bap'       => 'required|string',
            'tanggal'         => 'required|date',
            'depo'            => 'required|string',
            'cara_pemusnahan' => 'required|string',
        ]);

        DB::table('berita_acara')->insert([
            'nomor_bap'       => $request->nomor_bap,
            'tanggal'         => $request->tanggal,
            'jam'             => $request->jam,
            'depo'            => $request->depo,
            'region'          => $request->region ?? 'JABOTABEK',
            'tempat'          => $request->tempat,
            'cara_pemusnahan' => $request->cara_pemusnahan,
            'nama_pembuat'    => $request->nama_pembuat,
            'jabatan_pembuat' => $request->jabatan_pembuat,
            'saksi_1_nama'    => $request->saksi_1_nama,
            'saksi_1_jabatan' => $request->saksi_1_jabatan,
            'saksi_2_nama'    => $request->saksi_2_nama,
            'saksi_2_jabatan' => $request->saksi_2_jabatan,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return redirect()->route('berita-acara.index')->with('success', 'Berita Acara Pemusnahan berhasil dibuat!');
    }

    // Hapus BAP
    public function destroy($id)
    {
        DB::table('berita_acara')->where('id', $id)->delete();
        return redirect()->route('berita-acara.index')->with('success', 'Data Berita Acara berhasil dihapus!');
    }

    // Export Excel Berita Acara
    public function exportExcel($id)
    {
        $header = DB::table('berita_acara')->where('id', $id)->first();

        if (!$header) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $templatePath = storage_path('app/templates/Berita Acara Pemusnahan.xlsx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template Berita Acara Pemusnahan.xlsx tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Format Hari dan Tanggal Indonesia
        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal ? date('D', strtotime($header->tanggal)) : '';
        $indoDay = $days[$engDay] ?? '';

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthIndex = $header->tanggal ? (int)date('m', strtotime($header->tanggal)) : 0;
        $indoDate = $header->tanggal ? date('d', strtotime($header->tanggal)) . ' ' . $months[$monthIndex] . ' ' . date('Y', strtotime($header->tanggal)) : '-';

        // Mapping Data ke Sel Template Excel BAP
        $sheet->setCellValue('B2', 'No: ' . $header->nomor_bap);
        $sheet->setCellValue('E6', $header->depo);
        $sheet->setCellValue('E7', $header->region);
        $sheet->setCellValue('E8', $indoDay);
        $sheet->setCellValue('E9', $indoDate);
        $sheet->setCellValue('E10', $header->jam ?? '-');
        $sheet->setCellValue('E11', $header->tempat ?? $header->depo);
        $sheet->setCellValue('B14', 'dengan cara ' . $header->cara_pemusnahan);
        
        $sheet->setCellValue('E17', $header->nama_pembuat);
        $sheet->setCellValue('E18', $header->jabatan_pembuat);
        
        $sheet->setCellValue('E22', $header->saksi_1_nama);
        $sheet->setCellValue('E23', $header->saksi_1_jabatan);
        
        $sheet->setCellValue('K22', $header->saksi_2_nama);
        $sheet->setCellValue('K23', $header->saksi_2_jabatan);

        $filename = 'Berita_Acara_' . str_replace(['/', '\\', ' '], '_', $header->nomor_bap) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    // Export PDF Berita Acara
    public function exportPdf($id)
    {
        $header = DB::table('berita_acara')->where('id', $id)->first();

        if (!$header) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $templatePath = storage_path('app/templates/Berita Acara Pemusnahan.xlsx');
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'Template file tidak ditemukan.');
        }

        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(1);
        $sheet->setShowGridlines(false);

        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
        $engDay = $header->tanggal ? date('D', strtotime($header->tanggal)) : '';
        $indoDay = $days[$engDay] ?? '';

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthIndex = $header->tanggal ? (int)date('m', strtotime($header->tanggal)) : 0;
        $indoDate = $header->tanggal ? date('d', strtotime($header->tanggal)) . ' ' . $months[$monthIndex] . ' ' . date('Y', strtotime($header->tanggal)) : '-';

        $sheet->setCellValue('B2', 'No: ' . $header->nomor_bap);
        $sheet->setCellValue('E6', $header->depo);
        $sheet->setCellValue('E7', $header->region);
        $sheet->setCellValue('E8', $indoDay);
        $sheet->setCellValue('E9', $indoDate);
        $sheet->setCellValue('E10', $header->jam ?? '-');
        $sheet->setCellValue('E11', $header->tempat ?? $header->depo);
        $sheet->setCellValue('B14', 'dengan cara ' . $header->cara_pemusnahan);
        
        $sheet->setCellValue('E17', $header->nama_pembuat);
        $sheet->setCellValue('E18', $header->jabatan_pembuat);
        $sheet->setCellValue('E22', $header->saksi_1_nama);
        $sheet->setCellValue('E23', $header->saksi_1_jabatan);
        $sheet->setCellValue('K22', $header->saksi_2_nama);
        $sheet->setCellValue('K23', $header->saksi_2_jabatan);

        $filename = 'Berita_Acara_' . str_replace(['/', '\\', ' '], '_', $header->nomor_bap) . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Dompdf');
        $writer->save('php://output');
        exit;
    }
}