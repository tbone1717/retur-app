<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use App\Imports\ProdukImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $query = Produk::query();

        if ($request->has('cari') && $request->cari != '') {
            $query->where('sku', 'like', '%' . $request->cari . '%');
        }

        $produks = $query->get();

        return view('produk.index', compact('produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|unique:produks,sku',
            'uom' => 'required|numeric',
            'harga' => 'required|numeric',
            'status' => 'required',
        ]);

        Produk::create([
            'sku' => $request->sku,
            'keterangan' => $request->keterangan,
            'uom' => $request->uom,
            'harga' => $request->harga,
            'status' => $request->status,
            'barcode' => $request->barcode,
        ]);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $produk = Produk::findOrFail($id);

        $request->validate([
            'sku' => 'required|unique:produks,sku,' . $id,
            'uom' => 'required|numeric',
            'harga' => 'required|numeric',
            'status' => 'required',
        ]);

        $produk->update([
            'sku' => $request->sku,
            'keterangan' => $request->keterangan,
            'uom' => $request->uom,
            'harga' => $request->harga,
            'status' => $request->status,
            'barcode' => $request->barcode,
        ]);

        return redirect()->route('produk.index')->with('success', 'Data produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv',
        ]);

        Excel::import(new ProdukImport, $request->file('file_excel'));

        return redirect()->route('produk.index')->with('success', 'Data produk berhasil diimpor dari Excel!');
    }

    public function export()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Products');

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'SKU');
        $sheet->setCellValue('C1', 'Keterangan');
        $sheet->setCellValue('D1', 'UoM (Dus/Bal)');
        $sheet->setCellValue('E1', 'Harga');
        $sheet->setCellValue('F1', 'Status');
        $sheet->setCellValue('G1', 'Barcode');

        $produks = Produk::all();
        $rowNum = 2;
        $no = 1;

        foreach ($produks as $p) {
            $sheet->setCellValue("A{$rowNum}", $no++);
            $sheet->setCellValue("B{$rowNum}", (string)$p->sku);
            $sheet->setCellValue("C{$rowNum}", (string)$p->keterangan);
            $sheet->setCellValue("D{$rowNum}", $p->uom !== null ? (float)$p->uom : 1);
            $sheet->setCellValue("E{$rowNum}", $p->harga !== null ? (float)$p->harga : 0);
            $sheet->setCellValue("F{$rowNum}", (string)$p->status);
            
            $barcodeVal = $p->barcode !== null ? trim((string)$p->barcode) : '';
            if ($barcodeVal !== '') {
                $sheet->setCellValueExplicit("G{$rowNum}", $barcodeVal, DataType::TYPE_STRING);
                $sheet->getStyle("G{$rowNum}")->getNumberFormat()->setFormatCode('@');
            } else {
                $sheet->setCellValue("G{$rowNum}", null);
            }

            $rowNum++;
        }

        $fileName = 'data_produk_retur.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"{$fileName}\"");
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}