@extends('layouts.admin')

@section('title', 'Detail Mutasi GS to BS')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark m-0">
                <i class="bi bi-info-circle text-primary me-2"></i>Detail Mutasi: {{ $header->nomor_mutasi }}
            </h5>
            <span class="text-muted small">Rincian kerusakan produk mutasi GS ke BS.</span>
        </div>
        <a href="{{ route('mutasi-gs-bs.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row bg-light p-3 rounded border mb-4 g-3 small">
        <div class="col-md-3"><strong>Tanggal:</strong> {{ date('d-m-Y', strtotime($header->tanggal)) }}</div>
        <div class="col-md-3"><strong>Depo:</strong> {{ $header->depo }}</div>
        <div class="col-md-3"><strong>Minggu:</strong> {{ $header->minggu_ke }}</div>
        <div class="col-md-3"><strong>Keputusan:</strong> {{ $header->keputusan_transaksi }}</div>
    </div>

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
                    <th>LL</th>
                    <th>NED</th>
                    <th>Total PCS</th>
                    <th>Jumlah Harga</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @php $gPcs = 0; $gHarga = 0; @endphp
                @foreach($items as $i => $row)
                    @php $gPcs += $row->total_pcs; $gHarga += $row->jumlah_harga; @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td class="fw-bold">{{ $row->kode_produk }}</td>
                        <td class="text-center">{{ $row->qty_ed }}</td>
                        <td class="text-center">{{ $row->qty_hm }}</td>
                        <td class="text-center">{{ $row->qty_kr }}</td>
                        <td class="text-center">{{ $row->qty_ns }}</td>
                        <td class="text-center">{{ $row->qty_ll }}</td>
                        <td class="text-center">{{ $row->qty_ned }}</td>
                        <td class="text-center fw-bold text-primary">{{ $row->total_pcs }}</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($row->jumlah_harga, 0, ',', '.') }}</td>
                        <td>{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="8" class="text-end">GRAND TOTAL:</td>
                    <td class="text-center text-primary">{{ number_format($gPcs, 0, ',', '.') }}</td>
                    <td class="text-end text-danger fs-6">Rp {{ number_format($gHarga, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection