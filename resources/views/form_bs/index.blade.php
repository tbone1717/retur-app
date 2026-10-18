@extends('layouts.admin')

@section('title', 'Daftar Pengajuan BS Reguler')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-table text-primary me-2"></i>Daftar Pengajuan BS Reguler
        </h5>
        <a href="{{ route('pengajuan-bs.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Input Pengajuan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th>Kode SKU</th>
                    <th class="text-center">ED</th>
                    <th class="text-center">HM</th>
                    <th class="text-center">KR</th>
                    <th class="text-center">NS</th>
                    <th class="text-center">NED</th>
                    <th class="text-center">Total</th>
                    <th>Tanggal ED</th>
                    <th>Keterangan Proling</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $item->sku ?? $item->kode_produk ?? '-' }}</td>
                        <td class="text-center">{{ $item->qty_ed ?? 0 }}</td>
                        <td class="text-center">{{ $item->qty_hm ?? 0 }}</td>
                        <td class="text-center">{{ $item->qty_kr ?? 0 }}</td>
                        <td class="text-center">{{ $item->qty_ns ?? 0 }}</td>
                        <td class="text-center">{{ $item->qty_ned ?? 0 }}</td>
                        <td class="text-center fw-bold text-primary">{{ ($item->qty_ed ?? 0) + ($item->qty_hm ?? 0) + ($item->qty_kr ?? 0) + ($item->qty_ns ?? 0) + ($item->qty_ned ?? 0) }}</td>
                        <td>{{ $item->tanggal_ed ?? '-' }}</td>
                        <td>{{ $item->keterangan_proling ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada data pengajuan BS yang di-input.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection