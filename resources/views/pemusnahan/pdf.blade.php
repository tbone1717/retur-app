<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pemusnahan Karung</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 8px; }
        .header h3, .header h4 { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
        .sign-table { width: 100%; border: none; margin-top: 40px; }
        .sign-table td { border: none; text-align: center; vertical-align: top; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 15px; background: #dc3545; color: #fff; border: none; cursor: pointer; border-radius: 4px; font-weight: bold;">Cetak / Simpan PDF</button>
    </div>

    <div class="header">
        <h3>DEPO BEKASI UTAMA</h3>
        <h4>LAPORAN PENGAJUAN PEMUSNAHAN KARUNG / STOK</h4>
        <p style="margin: 5px 0 0 0; font-size: 11px;">
            Tanggal Cetak: {{ date('d M Y') }} 
            @if($tahap) | <strong>Filter: Tahap {{ $tahap }}</strong> @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="8%" class="text-center">[ &nbsp; ]</th>
                <th width="27%">No Karung</th>
                <th width="15%" class="text-center">Tahap</th>
                <th width="25%">Jenis Produk</th>
                <th width="20%">Total Isi Karung</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pemusnahans as $index => $p)
            @php 
                $qtyKarung = $p->karung->items->sum('qty');
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center"></td>
                <td><strong>{{ $p->karung->no_karung ?? '-' }}</strong></td>
                <td class="text-center">Tahap {{ $p->tahap }}</td>
                <td>{{ $p->karung->jenis_produk ?? '-' }}</td>
                <td>{{ $p->karung->items->count() ?? 0 }} SKU ({{ $qtyKarung }} Pcs)</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data pemusnahan karung.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 15px; font-weight: bold; font-size: 12px;">
        Total Karung: {{ count($pemusnahans) }} Karung
    </div>

    <table class="sign-table">
        <tr>
            <td width="50%">
                <p>Dibuat Oleh,</p>
                <br><br><br>
                <p><strong>( Checker )</strong></p>
            </td>
            <td width="50%">
                <p>Dicheck Oleh,</p>
                <br><br><br>
                <p><strong>( Picker )</strong></p>
            </td>
        </tr>
    </table>
</body>
</html>