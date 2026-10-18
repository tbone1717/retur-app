@extends('layouts.admin')

@section('title', 'Detail Pengajuan BS Reguler')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark m-0">
                <i class="bi bi-info-circle text-primary me-2"></i>Detail Pengajuan: {{ $header->nomor_pengajuan }}
            </h5>
            <span class="text-muted small">Rincian item SKU untuk nomor pengajuan ini.</span>
        </div>
        <a href="{{ route('form.bs.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- Informasi Header -->
    <div class="row bg-light p-3 rounded border mb-4 g-3 small">
        <div class="col-md-3"><strong>Tanggal:</strong> {{ $header->tanggal_pengajuan }}</div>
        <div class="col-md-3"><strong>Periode:</strong> {{ $header->periode }}</div>
        <div class="col-md-3"><strong>Depo Asal:</strong> {{ $header->depo_asal }}</div>
        <div class="col-md-3"><strong>Jenis:</strong> {{ $header->jenis_pemusnahan }}</div>
        <div class="col-md-3"><strong>Status:</strong> {{ $header->status_produk }}</div>
    </div>

    <!-- Tabel Rincian Item SKU -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle small">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>Kode SKU</th>
                    <th>ED</th>
                    <th>HM</th>
                    <th>KR</th>
                    <th>NS</th>
                    <th>NED</th>
                    <th>Tot PCS</th>
                    <th>Tot UoM</th>
                    <th>Jumlah Harga</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotalPcs = 0;
                    $grandTotalUom = 0;
                    $grandTotalHarga = 0;
                @endphp
                @foreach($items as $index => $row)
                    @php
                        // Kalkulasi manual total_uom agar terbulatkan saat ditampilkan
                        // jika datanya masih tersimpan berbentuk desimal lama di database
                        $produk = DB::table('produks')->where('sku', $row->kode_produk)->first();
                        $uomProduk = $produk ? ($produk->uom ?? $produk->isi_per_karton ?? $produk->konversi ?? 1) : 1;
                        if ($uomProduk <= 0) $uomProduk = 1;
                        $rowTotalUomBulat = round($row->total_pcs / $uomProduk);

                        $grandTotalPcs += $row->total_pcs;
                        $grandTotalUom += $rowTotalUomBulat;
                        $grandTotalHarga += $row->jumlah_harga;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $row->kode_produk }}</td>
                        <td class="text-center">{{ $row->qty_ed }}</td>
                        <td class="text-center">{{ $row->qty_hm }}</td>
                        <td class="text-center">{{ $row->qty_kr }}</td>
                        <td class="text-center">{{ $row->qty_ns }}</td>
                        <td class="text-center">{{ $row->qty_ned }}</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($row->total_pcs, 0, ',', '.') }}</td>
                        <td class="text-center fw-bold text-success">{{ number_format($rowTotalUomBulat, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($row->jumlah_harga, 0, ',', '.') }}</td>
                        <td>{{ $row->keterangan_proling ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <!-- FOOTER: GRAND TOTAL -->
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="7" class="text-end">GRAND TOTAL :</td>
                    <td class="text-center text-primary fs-6">{{ number_format($grandTotalPcs, 0, ',', '.') }}</td>
                    <td class="text-center text-success fs-6">{{ number_format($grandTotalUom, 0, ',', '.') }}</td>
                    <td class="text-end text-danger fs-6">Rp {{ number_format($grandTotalHarga, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection