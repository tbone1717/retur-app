<?php

namespace App\Imports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProdukImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Menyesuaikan penamaan header di file Excel Anda ('uom_dusbal' atau 'uom')
        $uomVal = $row['uom_dusbal'] ?? $row['uom'] ?? $row['UoM (Dus/Bal)'] ?? 1;

        // Mencegah duplikasi berdasarkan SKU
        return Produk::updateOrCreate(
            ['sku' => $row['sku']],
            [
                'keterangan' => $row['keterangan'] ?? null,
                'uom'        => is_numeric($uomVal) ? $uomVal : 1,
                'harga' => $row['harga'] ?? 0,
                'status' => $row['status'] ?? 'Aktif',
                'barcode' => isset($row['barcode']) ? (string)$row['barcode'] : null,
            ]
        );
    }
}