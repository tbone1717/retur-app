<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProdukExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Produk::all();
    }

    // Menentukan judul header di baris pertama Excel
    public function headings(): array
    {
        return [
            'No',
            'SKU',
            'Keterangan',
            'Harga',
            'Status',
            'Barcode',
        ];
    }

    // Memetakan setiap baris data agar nomor urut otomatis muncul kembali
    public function map($row): array
    {
        static $no = 1;
        return [
            $no++,
            $row->sku,
            $row->keterangan,
            $row->uom,
            $row->harga,
            $row->status,
            $row->barcode,
        ];
    }
}