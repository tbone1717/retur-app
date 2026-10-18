@extends('layouts.admin')

@section('title', 'History Retur - Bulanan')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark m-0"><i class="bi bi-clock-history text-primary me-2"></i>History Retur BS (Bulanan)</h5>
            <p class="text-muted small mb-0">Pilih bulan transaksi untuk melihat rincian tanggal retur.</p>
        </div>
        <a href="{{ route('retur.dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">No</th>
                    <th>Bulan (YYYY-MM)</th>
                    <th>Total Retur</th>
                    <th class="text-center" width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulanList as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $row->bulan }}</td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary px-3 py-1 fw-bold rounded-pill">
                            {{ $row->total_retur }} Transaksi
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('retur.history.tanggal', $row->bulan) }}" class="btn btn-sm btn-light text-primary fw-semibold" title="Lihat Tanggal">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                        <a href="{{ route('retur.export.excel') }}" class="btn btn-sm btn-light text-success fw-semibold" title="Download Excel">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted py-4">Belum ada history retur tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection