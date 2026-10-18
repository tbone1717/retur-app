<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Pengajuan Pemusnahan BS - {{ $header->nomor_pengajuan }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 8pt; color: #000; margin: 5px; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
        .header-table td { padding: 2px 3px; vertical-align: top; }
        
        .main-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        .main-table th, .main-table td { border: 1px solid #000; padding: 3px 4px; text-align: center; vertical-align: middle; word-wrap: break-word; }
        .main-table th { background-color: #f2f2f2; font-weight: bold; font-size: 7.5pt; }
        
        .text-start { text-align: left; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .footer-section { width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 7.5pt; }
        .footer-section td { vertical-align: top; }
        
        .sig-table { width: 100%; border-collapse: collapse; margin-top: 10px; text-align: center; font-size: 7.5pt; }
        .sig-table th, .sig-table td { border: 1px solid #000; padding: 3px; }
        .sig-table th { background-color: #f2f2f2; }
    </style>
</head>
<body>

    <!-- Header Dokumen -->
    <table class="header-table">
        <tr>
            <td width="55%"></td>
            <td width="20%" class="fw-bold text-end" style="font-size: 10pt;">FORMULIR</td>
            <td width="25%" class="fw-bold" style="font-size: 9pt;">NO. DOKUMEN : FR-C3.1.1-2-01-01</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="2" class="fw-bold" style="font-size: 11pt;">PENGAJUAN PEMUSNAHAN BS</td>
        </tr>
    </table>

    <table class="header-table" style="font-size: 9pt; margin-bottom: 8px;">
        <tr>
            <td width="15%"><strong>Nomor</strong></td>
            <td width="85%"><strong>: {{ $header->nomor_pengajuan }}</strong></td>
        </tr>
        <tr>
            <td><strong>Depo</strong></td>
            <td><strong>: {{ $header->depo_asal }}</strong></td>
        </tr>
        <tr>
            <td><strong>Region</strong></td>
            <td><strong>: Jabobeka</strong></td>
        </tr>
        <tr>
            <td><strong>Hari/ Tanggal</strong></td>
            @php
                $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                $engDay = $header->tanggal_pengajuan ? date('D', strtotime($header->tanggal_pengajuan)) : '';
                $indoDay = $days[$engDay] ?? '';
                $hariTanggal = $header->tanggal_pengajuan ? $indoDay . ' / ' . date('d-m-Y', strtotime($header->tanggal_pengajuan)) : '-';
            @endphp
            <td><strong>: {{ $hariTanggal }}</strong></td>
        </tr>
        <tr>
            <td><strong>Periode</strong></td>
            <td><strong>: {{ $header->periode }}</strong></td>
        </tr>
        <tr>
            <td><strong>Jenis Pemusnahan</strong></td>
            <td><strong>: {{ $header->jenis_pemusnahan }}</strong></td>
        </tr>
        <tr>
            <td><strong>Status Produk</strong></td>
            <td><strong>: {{ $header->status_produk }}</strong></td>
        </tr>
    </table>

    <div style="margin-bottom: 5px; font-size: 8.5pt;">Telah dilakukan pengajuan pemusnahan produk di Gudang Bad Stock (BS) dengan rincian Nama Produk dan penyebabnya sebagai berikut :</div>

    <!-- Tabel Utama Sesuai Template Excel -->
    <table class="main-table">
        <thead>
            <tr>
                <th rowspan="3" width="3%">NO</th>
                <th rowspan="3" width="10%">Kode Produk</th>
                <th colspan="5">Alasan Pemusnahan (Pcs) *)</th>
                <th colspan="2">Total Pengajuan</th>
                <th rowspan="3" width="11%">Jumlah Harga (Rp)**)</th>
                <th colspan="2">KETERANGAN (TANGGAL ED)</th>
                <th rowspan="3" width="14%">KETERANGAN PROLING dan Stok SUDAH ED</th>
                <th rowspan="3" width="7%">Approved Extraordinary</th>
            </tr>
            <tr>
                <th rowspan="2" width="4%">ED</th>
                <th rowspan="2" width="4%">HM</th>
                <th rowspan="2" width="4%">KR</th>
                <th rowspan="2" width="4%">NS</th>
                <th rowspan="2" width="4%">NED</th>
                <th rowspan="2" width="5%">In PCS</th>
                <th rowspan="2" width="5%">In Uom (Dus/Bal)</th>
                <th rowspan="2" width="5%">Kode ED</th>
                <th rowspan="2" width="5%">Validasi EO</th>
            </tr>
            <tr></tr>
        </thead>
        <tbody>
            @php
                $totEd = 0; $totHm = 0; $totKr = 0; $totNs = 0; $totNed = 0;
                $totPcsAll = 0; $totUomAll = 0; $totHargaAll = 0;
            @endphp
            @foreach($items as $i => $row)
                @php
                    $totEd += $row->qty_ed;
                    $totHm += $row->qty_hm;
                    $totKr += $row->qty_kr;
                    $totNs += $row->qty_ns;
                    $totNed += $row->qty_ned;
                    $totPcsAll += $row->total_pcs;
                    $totUomAll += $row->total_uom;
                    $totHargaAll += $row->jumlah_harga;
                @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="text-start fw-bold">{{ $row->kode_produk }}</td>
                    <td>{{ $row->qty_ed > 0 ? $row->qty_ed : '' }}</td>
                    <td>{{ $row->qty_hm > 0 ? $row->qty_hm : '' }}</td>
                    <td>{{ $row->qty_kr > 0 ? $row->qty_kr : '' }}</td>
                    <td>{{ $row->qty_ns > 0 ? $row->qty_ns : '' }}</td>
                    <td>{{ $row->qty_ned > 0 ? $row->qty_ned : '' }}</td>
                    <td class="fw-bold">{{ $row->total_pcs }}</td>
                    <td>{{ $row->total_uom }}</td>
                    <td class="text-end">{{ number_format($row->jumlah_harga, 0, ',', '.') }}</td>
                    <td></td>
                    <td></td>
                    <td class="text-start">{{ $row->keterangan_proling ?? '-' }}</td>
                    <td>0.0</td>
                </tr>
            @endforeach

            <!-- Baris Total -->
            <tr class="fw-bold" style="background-color: #e6e6e6;">
                <td colspan="2" class="text-end">TOTAL :</td>
                <td>{{ $totEd > 0 ? $totEd : '' }}</td>
                <td>{{ $totHm > 0 ? $totHm : '' }}</td>
                <td>{{ $totKr > 0 ? $totKr : '' }}</td>
                <td>{{ $totNs > 0 ? $totNs : '' }}</td>
                <td>{{ $totNed > 0 ? $totNed : '' }}</td>
                <td>{{ $totPcsAll }}</td>
                <td>{{ $totUomAll }}</td>
                <td class="text-end">{{ number_format($totHargaAll, 0, ',', '.') }}</td>
                <td colspan="4"></td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan Kaki & Akumulasi Week -->
    <table class="footer-section">
        <tr>
            <td width="55%">
                Note : *) Jumlah kerusakan : dalam satuan Pcs dan UOM (Dus/Bal)<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;**) Jumlah Harga = Total jumlah kerusakan x Harga Retail<br><br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ED : Expired Date<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;HM : Hama<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;KR : Kerusakan Produk<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;NS : Non Spec<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;LL : Lain - Lain
            </td>
            <td width="45%">
                <table class="main-table" style="font-size: 7.5pt; width: 100%;">
                    <tr style="background-color: #f2f2f2;"><td colspan="5" class="fw-bold">Akumulasi Pemusnahan yang telah dilakukan 1)</td></tr>
                    <tr style="background-color: #f2f2f2;" class="fw-bold"><td>Minggu</td><td>Total Pengajuan</td><td>Jual</td><td>Ratio BS</td><td></td></tr>
                    <tr><td>Week 1</td><td class="text-end">{{ number_format($weekTotals['week 1'], 0, ',', '.') }}</td><td></td><td></td><td></td></tr>
                    <tr><td>Week 2</td><td class="text-end">{{ number_format($weekTotals['week 2'], 0, ',', '.') }}</td><td></td><td></td><td></td></tr>
                    <tr><td>Week 3</td><td class="text-end">{{ number_format($weekTotals['week 3'], 0, ',', '.') }}</td><td></td><td></td><td></td></tr>
                    <tr><td>Week 4</td><td class="text-end">{{ number_format($weekTotals['week 4'], 0, ',', '.') }}</td><td></td><td></td><td></td></tr>
                    <tr><td>Week 5</td><td class="text-end">{{ number_format($weekTotals['week 5'], 0, ',', '.') }}</td><td></td><td></td><td></td></tr>
                    <tr class="fw-bold" style="background-color: #e6e6e6;"><td>Total Rekap</td><td class="text-end">{{ number_format(array_sum($weekTotals), 0, ',', '.') }}</td><td class="text-end">0</td><td></td><td></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Blok Tanda Tangan -->
    <table class="sig-table">
        <tr class="fw-bold">
            <td width="20%">Dibuat Oleh,</td>
            <td width="20%">Diajukan oleh,</td>
            <td width="20%">Diperiksa oleh,</td>
            <td width="20%">Mengetahui,</td>
            <td width="20%">Menyetujui</td>
        </tr>
        <tr style="height: 40px;">
            <td></td><td></td><td></td><td></td><td></td>
        </tr>
        <tr class="fw-bold">
            <td>Airlangga</td>
            <td>Aditya Yudha</td>
            <td>Ninin Mulyati</td>
            <td>Ridha.Setiawan</td>
            <td>Andri Panca / Umi / Nuraeny</td>
        </tr>
        <tr style="font-size: 6.5pt; color: #555;">
            <td>Checker BS</td>
            <td>BLC</td>
            <td>Koor. Admin</td>
            <td>RLH</td>
            <td>Branch Head / RFAH 2) / Regional Head 2)</td>
        </tr>
    </table>

    <div style="font-size: 6.5pt; margin-top: 8px;">
        Note : 1) Jika Pengajuan merupakan Biaya SNS<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2) Jika Total Rekap lebih besar dari ketentuan yang telah ditetapkan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Tudung Group</strong><br>
        23 Maret 2016 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No. Rev. 0.0 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Page : 1/1
    </div>

</body>
</html>