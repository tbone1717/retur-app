<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\B2b;
use App\Models\B2bItem;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class B2BController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalBulanIni = B2b::whereMonth('tanggal_bon_in', Carbon::now()->month)->count();
        $totalPending = B2b::where('status', 'pending')->count();
        $totalSudahRetur = B2b::where('status', 'sudah retur')->count();
        $totalSat = B2b::where('outlet', 'SAT')->count();
        $totalMidi = B2b::where('outlet', 'MIDI')->count();

        $b2bTerbaru = B2b::with('items')->latest('tanggal_bon_in')->take(5)->get();

        $skuTerbanyak = B2bItem::select('sku', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(sum_price) as total_harga'))
            ->groupBy('sku')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return view('b2b.dashboard', compact('totalBulanIni', 'totalPending', 'totalSudahRetur', 'totalSat', 'totalMidi', 'b2bTerbaru'));
    }

    public function input()
    {
        $produks = \App\Models\Produk::all();
        
        // Memformat data menjadi JSON di Controller agar View bersih
        $produkJson = $produks->keyBy('sku')->map(function($p) {
            return [
                'harga' => $p->harga ?? $p->price ?? 0,
                'keterangan' => $p->keterangan ?? $p->nama_produk ?? ''
            ];
        });

        return view('b2b.input', compact('produks', 'produkJson'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_bon_in' => 'required|date',
            'nama_toko' => 'required|string',
            'outlet' => 'required|in:SAT,MIDI',
            'items' => 'required|array|min:1',
        ]);

        $b2b = B2b::create([
            'tanggal_bon_in' => $request->tanggal_bon_in,
            'nama_toko' => $request->nama_toko,
            'nrb_outlet' => $request->nrb_outlet,
            'outlet' => $request->outlet,
            'status' => 'pending',
            'tanggal_bon' => $request->tanggal_bon,
        ]);

        foreach ($request->items as $item) {
            if (empty($item['sku'])) {
                continue;
            }

            B2bItem::create([
                'b2b_id' => $b2b->id,
                'sku' => $item['sku'],
                'qty' => $item['qty'],
                'status' => $item['status'] ?? null,
                'warning' => $item['warning'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'sum_price' => $item['sum_price'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('b2b.dashboard')->with('success', 'Data transaksi B2B berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_bon_in' => 'required|date',
            'nama_toko' => 'required|string',
            'outlet' => 'required|in:SAT,MIDI',
            'items' => 'required|array|min:1',
        ]);

        $b2b = B2b::findOrFail($id);
        $b2b->update([
            'tanggal_bon_in' => $request->tanggal_bon_in,
            'nama_toko' => $request->nama_toko,
            'nrb_outlet' => $request->nrb_outlet,
            'outlet' => $request->outlet,
            'tanggal_bon' => $request->tanggal_bon,
        ]);

        $b2b->items()->delete();

        foreach ($request->items as $item) {
            if (empty($item['sku'])) {
                continue;
            }

            B2bItem::create([
                'b2b_id' => $b2b->id,
                'sku' => $item['sku'],
                'qty' => $item['qty'],
                'status' => $item['status'] ?? null,
                'warning' => $item['warning'] ?? null,
                'barcode' => $item['barcode'] ?? null,
                'sum_price' => $item['sum_price'],
                'keterangan' => $item['keterangan'] ?? null,
            ]);
        }

        return redirect()->route('b2b.history.detail', $b2b->tanggal_bon_in)->with('success', 'Data transaksi B2B berhasil diperbarui.');
    }

    public function historyIndex()
    {
        $bulanList = B2b::select(DB::raw("strftime('%Y-%m', tanggal_bon_in) as bulan"), DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('bulan')
            ->orderByDesc('bulan')
            ->get();

        return view('b2b.history_index', compact('bulanList'));
    }

    public function historyTanggal($bulan)
    {
        $tanggalList = B2b::where(DB::raw("strftime('%Y-%m', tanggal_bon_in)"), $bulan)
            ->select('tanggal_bon_in', DB::raw('COUNT(DISTINCT nama_toko) as total_toko'), DB::raw('COUNT(*) as total_transaksi'))
            ->groupBy('tanggal_bon_in')
            ->orderByDesc('tanggal_bon_in')
            ->get();

        return view('b2b.history_tanggal', compact('bulan', 'tanggalList'));
    }

    public function historyDetail(Request $request, $tanggal)
    {
        $query = B2b::with('items')->where('tanggal_bon_in', $tanggal);

        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_toko', 'like', '%' . $request->cari . '%');
        }

        $b2bs = $query->get();
        $totalToko = B2b::where('tanggal_bon_in', $tanggal)->distinct('nama_toko')->count('nama_toko');

        return view('b2b.history_detail', compact('tanggal', 'b2bs', 'totalToko'));
    }

    public function edit($id)
    {
        $b2b = B2b::with('items')->findOrFail($id);
        $produks = Produk::all();
        return view('b2b.edit', compact('b2b', 'produks'));
    }


    public function destroy($id)
    {
        $b2b = B2b::findOrFail($id);
        $tanggal = $b2b->tanggal_bon_in;
        $b2b->delete();

        return redirect()->route('b2b.history.detail', $tanggal)->with('success', 'Data transaksi B2B berhasil dihapus.');
    }

    public function indexSat(Request $request)
    {
        $query = B2b::where('outlet', 'SAT')->with('items');

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_toko', 'like', '%' . $request->cari . '%')
                  ->orWhere('nrb_outlet', 'like', '%' . $request->cari . '%');
            });
        }

        $b2bs = $query->latest('tanggal_bon_in')->paginate(10);
        $outlet = 'SAT';
        return view('b2b.sat', compact('b2bs', 'outlet'));
    }

    public function indexMidi(Request $request)
    {
        $query = B2b::where('outlet', 'MIDI')->with('items');

        if ($request->filled('cari')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_toko', 'like', '%' . $request->cari . '%')
                  ->orWhere('nrb_outlet', 'like', '%' . $request->cari . '%');
            });
        }

        $b2bs = $query->latest('tanggal_bon_in')->paginate(10);
        $outlet = 'MIDI';
        return view('b2b.midi', compact('b2bs', 'outlet'));
    }

    public function updateStatus(Request $request, $id)
    {
        $b2b = B2b::findOrFail($id);
        $b2b->status = $request->status;
        $b2b->save();

        return back()->with('success', 'Status transaksi B2B berhasil diperbarui.');
    }

    public function show($id)
    {
        $b2b = B2b::with('items')->findOrFail($id);
        return view('b2b.show', compact('b2b'));
    }

    public function exportExcel($bulan = null)
    {
        $query = B2b::with('items');

        if ($bulan) {
            $query->where(DB::raw("strftime('%Y-%m', tanggal_bon_in)"), $bulan);
            $fileName = 'b2b-' . $bulan . '.xlsx';
            $periodeLabel = $bulan;
        } else {
            $fileName = 'b2b-semua-' . date('Y-m-d') . '.xlsx';
            $periodeLabel = 'SEMUA';
        }

        $b2bs = $query->get();

        $totalTransaksi = $b2bs->count();
        $totalQty = 0;
        $grandTotal = 0;
        $statusSummary = [];
        $tanggalSummary = [];

        foreach ($b2bs as $b2b) {
            $tgl = $b2b->tanggal_bon_in;
            if (!isset($tanggalSummary[$tgl])) {
                $tanggalSummary[$tgl] = 0;
            }

            foreach ($b2b->items as $item) {
                $qty = $item->qty ?? 0;
                $price = $item->sum_price ?? 0;
                $status = $item->status ?? '-';

                $totalQty += $qty;
                $grandTotal += $price;

                if (!isset($statusSummary[$status])) {
                    $statusSummary[$status] = 0;
                }
                $statusSummary[$status] += $price;

                $tanggalSummary[$tgl] += $price;
            }
        }

        $spreadsheet = new Spreadsheet();

        $sheetDetail = $spreadsheet->getActiveSheet();
        $sheetDetail->setTitle('DETAIL B2B');

        $headers = ['Tanggal Bon In', 'Nama Toko', 'SKU', 'Qty', 'NRB Outlet', 'Tanggal Bon', 'Status', 'Barcode', 'Sum Price', 'Keterangan'];
        $sheetDetail->fromArray($headers, NULL, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9']
            ]
        ];
        $sheetDetail->getStyle('A1:J1')->applyFromArray($headerStyle);

        $rowNum = 2;
        foreach ($b2bs as $b2b) {
            foreach ($b2b->items as $item) {
                $sheetDetail->setCellValue('A' . $rowNum, $b2b->tanggal_bon_in);
                $sheetDetail->setCellValue('B' . $rowNum, $b2b->nama_toko);
                $sheetDetail->setCellValue('C' . $rowNum, $item->sku);
                $sheetDetail->setCellValue('D' . $rowNum, $item->qty ?? 0);
                $sheetDetail->setCellValue('E' . $rowNum, $b2b->nrb_outlet ?? '');
                $sheetDetail->setCellValue('F' . $rowNum, $b2b->tanggal_bon ?? '');
                $sheetDetail->setCellValue('G' . $rowNum, $item->status ?? '');
                $sheetDetail->setCellValue('H' . $rowNum, $item->barcode ?? '');
                $sheetDetail->setCellValue('I' . $rowNum, $item->sum_price ?? 0);
                $sheetDetail->setCellValue('J' . $rowNum, $item->keterangan ?? '');

                $statusUpper = strtoupper($item->status ?? '');
                $statusFillColor = 'FFFFFF';
                if (str_contains($statusUpper, 'EXISTING')) $statusFillColor = '93DD44';
                elseif (str_contains($statusUpper, 'PROCHIZ')) $statusFillColor = 'FFFF00';
                elseif (str_contains($statusUpper, 'NPL')) $statusFillColor = 'FFC7CE';

                if ($statusFillColor !== 'FFFFFF') {
                    $sheetDetail->getStyle('G' . $rowNum)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($statusFillColor);
                }

                $price = $item->sum_price ?? 0;
                $sheetDetail->getStyle('I' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                $priceFontColor = ($price > 1000000) ? 'FF0000' : '008000';
                $sheetDetail->getStyle('I' . $rowNum)->getFont()->getColor()->setRGB($priceFontColor);

                $rowNum++;
            }
        }

        foreach (range('A', 'J') as $col) {
            $sheetDetail->getColumnDimension($col)->setAutoSize(true);
        }

        $sheetSummary = $spreadsheet->createSheet();
        $sheetSummary->setTitle('SUMMARY');

        $sheetSummary->setCellValue('A1', 'LAPORAN B2B BULAN');
        $sheetSummary->setCellValue('B1', $periodeLabel);
        $sheetSummary->getStyle('A1:B1')->getFont()->setBold(true);

        $sheetSummary->setCellValue('A3', 'Total Transaksi');
        $sheetSummary->setCellValue('B3', $totalTransaksi);

        $sheetSummary->setCellValue('A4', 'Total Qty');
        $sheetSummary->setCellValue('B4', $totalQty);

        $sheetSummary->setCellValue('A5', 'Grand Total');
        $sheetSummary->setCellValue('B5', $grandTotal);
        $sheetSummary->getStyle('A5:B5')->getFont()->setBold(true);
        $sheetSummary->getStyle('B5')->getNumberFormat()->setFormatCode('"Rp" #,##0');

        $sheetSummary->setCellValue('A7', 'TOTAL PER STATUS');
        $sheetSummary->getStyle('A7')->getFont()->setBold(true);

        $rowIdx = 8;
        foreach ($statusSummary as $status => $sumPrice) {
            $sheetSummary->setCellValue('A' . $rowIdx, $status);
            $sheetSummary->setCellValue('B' . $rowIdx, $sumPrice);
            $sheetSummary->getStyle('B' . $rowIdx)->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $rowIdx++;
        }

        $rowIdx++;
        $sheetSummary->setCellValue('A' . $rowIdx, 'TOTAL PER TANGGAL');
        $sheetSummary->getStyle('A' . $rowIdx)->getFont()->setBold(true);
        $rowIdx++;

        foreach ($tanggalSummary as $tgl => $sumPrice) {
            $sheetSummary->setCellValue('A' . $rowIdx, $tgl);
            $sheetSummary->setCellValue('B' . $rowIdx, $sumPrice);
            $sheetSummary->getStyle('B' . $rowIdx)->getNumberFormat()->setFormatCode('"Rp" #,##0');
            $rowIdx++;
        }

        $sheetSummary->getColumnDimension('A')->setAutoSize(true);
        $sheetSummary->getColumnDimension('B')->setAutoSize(true);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}