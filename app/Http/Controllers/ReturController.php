<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Retur;
use App\Models\ReturItem;
use App\Models\Produk;
use App\Models\B2b;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReturController extends Controller
{
    public function dashboard(Request $request)
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $totalHariIni = Retur::whereDate('tanggal_bon_in', $today)->count();
        $totalMingguIni = Retur::where('tanggal_bon_in', '>=', $startOfWeek)->count();
        $totalBulanIni = Retur::where('tanggal_bon_in', '>=', $startOfMonth)->count();

        $search = $request->input('search');

        // Menggabungkan Eager Loading, Pencarian, & Pagination pada $returTerbaru 
        // agar mendukung ribuan data (1,000+) dan view links() berjalan sempurna.
        $returTerbaru = Retur::with('items')
            ->when($search, function ($query, $search) {
                return $query->where('nama_toko', 'like', "%{$search}%")
                             ->orWhere('driver', 'like', "%{$search}%")
                             ->orWhere('b2b', 'like', "%{$search}%")
                             ->orWhere('nrb_outlet', 'like', "%{$search}%");
            })
            ->latest('tanggal_bon_in')
            ->paginate(10)
            ->withQueryString();

        $skuTerbanyak = ReturItem::select('sku', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(sum_price) as total_harga'))
            ->groupBy('sku')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return view('retur.dashboard', compact(
            'totalHariIni', 
            'totalMingguIni', 
            'totalBulanIni', 
            'returTerbaru', 
            'skuTerbanyak', 
            'search'
        ));
    }

    public function input()
    {
        $produks = Produk::all()->keyBy('sku')->map(function($p) {
            return [
                'harga' => $p->harga ?? $p->price ?? 0,
                'status' => $p->status ?? '-',
                'barcode' => $p->barcode ?? '',
                'keterangan' => $p->keterangan ?? $p->nama_produk ?? ''
            ];
        });

        $b2bs = B2b::where('status', '!=', 'sudah retur')->with('items')->get()->keyBy('nrb_outlet')->map(function($b) {
            return [
                'nrb' => $b->nrb_outlet,
                'nama_toko' => $b->nama_toko,
                'items' => $b->items->map(function($item) {
                    return [
                        'sku' => $item->sku,
                        'qty' => $item->qty,
                        'keterangan' => $item->keterangan ?? ''
                    ];
                })->values()
            ];
        });

        return view('retur.input', compact('produks', 'b2bs'));
    }

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
            if (empty($item['sku'])) {
                continue;
            }

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

        if ($request->filled('b2b')) {
            $b2bInput = trim($request->b2b);
            
            $b2bRecord = B2b::where('nrb_outlet', $b2bInput)
                         ->orWhere('nrb_outlet', 'like', '%' . $b2bInput)
                         ->first();

            if ($b2bRecord) {
                $b2bRecord->update([
                    'status' => 'sudah retur'
                ]);
            }
        }

        return redirect()->route('retur.dashboard')->with('success', 'Transaksi retur berhasil disimpan dan status B2B diperbarui.');
    }

    public function historyIndex()
    {
        $bulanList = Retur::select(DB::raw("strftime('%Y-%m', tanggal_bon_in) as bulan"), DB::raw('COUNT(*) as total_retur'))
            ->groupBy('bulan')
            ->orderByDesc('bulan')
            ->get();

        return view('retur.history_index', compact('bulanList'));
    }

    public function historyTanggal($bulan)
    {
        $tanggalList = Retur::where(DB::raw("strftime('%Y-%m', tanggal_bon_in)"), $bulan)
            ->select('tanggal_bon_in', DB::raw('COUNT(DISTINCT nama_toko) as total_toko'), DB::raw('COUNT(*) as total_retur'))
            ->groupBy('tanggal_bon_in')
            ->orderByDesc('tanggal_bon_in')
            ->get();

        return view('retur.history_tanggal', compact('bulan', 'tanggalList'));
    }

    public function historyDetail(Request $request, $tanggal)
    {
        $query = Retur::with('items')->where('tanggal_bon_in', $tanggal);

        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_toko', 'like', '%' . $request->cari . '%');
        }

        $returs = $query->get();
        $totalToko = Retur::where('tanggal_bon_in', $tanggal)->distinct('nama_toko')->count('nama_toko');

        return view('retur.history_detail', compact('tanggal', 'returs', 'totalToko'));
    }

    public function edit($id)
    {
        $retur = Retur::with('items')->findOrFail($id);
        $produks = Produk::all();
        $b2bs = B2b::where('status', '!=', 'sudah retur')
                    ->orWhere('nrb_outlet', $retur->b2b)
                    ->with('items')
                    ->get();
                    
        return view('retur.edit', compact('retur', 'produks', 'b2bs'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_bon_in' => 'required|date',
            'nama_toko' => 'required|string',
            'driver' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        $retur = Retur::findOrFail($id);
        $retur->update([
            'tanggal_bon_in' => $request->tanggal_bon_in,
            'nama_toko' => $request->nama_toko,
            'driver' => $request->driver,
            'b2b' => $request->b2b,
            'nrb_outlet' => $request->nrb_outlet,
            'tanggal_bon' => $request->tanggal_bon,
        ]);

        $retur->items()->delete();

        foreach ($request->items as $item) {
            if (empty($item['sku'])) {
                continue;
            }

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

        return redirect()->route('retur.history.detail', $retur->tanggal_bon_in)->with('success', 'Data Retur BS berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $retur = Retur::findOrFail($id);
        $tanggal = $retur->tanggal_bon_in;
        $retur->delete();

        return redirect()->route('retur.history.detail', $tanggal)->with('success', 'Data Retur BS berhasil dihapus.');
    }

    public function exportExcel($bulan = null)
    {
        $query = Retur::with('items');

        if ($bulan) {
            $query->where(DB::raw("strftime('%Y-%m', tanggal_bon_in)"), $bulan);
            $fileName = 'retur-bs-' . $bulan . '.xlsx';
            $periodeLabel = $bulan;
        } else {
            $fileName = 'retur-bs-semua-' . date('Y-m-d') . '.xlsx';
            $periodeLabel = 'SEMUA';
        }

        $returs = $query->get();

        $totalRetur = $returs->count();
        $totalQty = 0;
        $grandTotal = 0;
        $statusSummary = [];
        $tanggalSummary = [];

        foreach ($returs as $retur) {
            $tgl = $retur->tanggal_bon_in;
            if (!isset($tanggalSummary[$tgl])) {
                $tanggalSummary[$tgl] = 0;
            }

            foreach ($retur->items as $item) {
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
        $sheetDetail->setTitle('DETAIL RETUR');

        $headers = ['Tanggal Bon In', 'Nama Toko', 'SKU', 'Qty', 'NRB Outlet', 'Tanggal Bon', 'Driver', 'Status', 'Barcode', 'Sum Price', 'Keterangan'];
        $sheetDetail->fromArray($headers, NULL, 'A1');

        $headerStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9']
            ]
        ];
        $sheetDetail->getStyle('A1:K1')->applyFromArray($headerStyle);

        $rowNum = 2;
        foreach ($returs as $retur) {
            foreach ($retur->items as $item) {
                $sheetDetail->setCellValue('A' . $rowNum, $retur->tanggal_bon_in);
                $sheetDetail->setCellValue('B' . $rowNum, $retur->nama_toko);
                $sheetDetail->setCellValue('C' . $rowNum, $item->sku);
                $sheetDetail->setCellValue('D' . $rowNum, $item->qty ?? 0);
                $sheetDetail->setCellValue('E' . $rowNum, $retur->nrb_outlet ?? '');
                $sheetDetail->setCellValue('F' . $rowNum, $retur->tanggal_bon ?? '');
                $sheetDetail->setCellValue('G' . $rowNum, $retur->driver);
                $sheetDetail->setCellValue('H' . $rowNum, $item->status ?? '');
                $sheetDetail->setCellValue('I' . $rowNum, $item->barcode ?? '');
                $sheetDetail->setCellValue('J' . $rowNum, $item->sum_price ?? 0);
                $sheetDetail->setCellValue('K' . $rowNum, $item->keterangan ?? '');

                $statusUpper = strtoupper($item->status ?? '');
                $statusFillColor = 'FFFFFF';
                if (str_contains($statusUpper, 'EXISTING')) $statusFillColor = '93DD44';
                elseif (str_contains($statusUpper, 'PROCHIZ')) $statusFillColor = 'FFFF00';
                elseif (str_contains($statusUpper, 'NPL')) $statusFillColor = 'FFC7CE';

                if ($statusFillColor !== 'FFFFFF') {
                    $sheetDetail->getStyle('H' . $rowNum)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB($statusFillColor);
                }

                $price = $item->sum_price ?? 0;
                $sheetDetail->getStyle('J' . $rowNum)->getNumberFormat()->setFormatCode('"Rp" #,##0');
                
                $priceFontColor = ($price >= 1000000) ? 'FF0000' : '008000';
                $sheetDetail->getStyle('J' . $rowNum)->getFont()->getColor()->setRGB($priceFontColor);

                $rowNum++;
            }
        }

        foreach (range('A', 'K') as $col) {
            $sheetDetail->getColumnDimension($col)->setAutoSize(true);
        }

        $sheetSummary = $spreadsheet->createSheet();
        $sheetSummary->setTitle('SUMMARY');

        $sheetSummary->setCellValue('A1', 'LAPORAN RETUR BULAN');
        $sheetSummary->setCellValue('B1', $periodeLabel);
        $sheetSummary->getStyle('A1:B1')->getFont()->setBold(true);

        $sheetSummary->setCellValue('A3', 'Total Retur');
        $sheetSummary->setCellValue('B3', $totalRetur);

        $sheetSummary->setCellValue('A4', 'Total Qty');
        $sheetSummary->setCellValue('B4', $totalQty);

        $sheetSummary->setCellValue('A5', 'Grand Total');
        $sheetSummary->setCellValue('B5', $grandTotal);
        
        $sheetSummary->getStyle('A5:B5')->getFont()->setBold(true);
        $sheetSummary->getStyle('B5')->getNumberFormat()->setFormatCode('"Rp" #,##0');
        
        $grandTotalColor = ($grandTotal >= 1000000) ? 'FF0000' : '008000';
        $sheetSummary->getStyle('B5')->getFont()->getColor()->setRGB($grandTotalColor);

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

        $chunkTanggal = $tanggalSummary;
        foreach ($chunkTanggal as $tgl => $sumPrice) {
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