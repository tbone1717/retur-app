@extends('layouts.admin')

@section('title', 'History Retur - Tanggal')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-calendar-date text-primary me-2"></i>History Per Tanggal: {{ $bulan }}</h5>
            <p class="text-muted small mb-0">Daftar tanggal transaksi retur pada bulan terpilih.</p>
        </div>
        <a href="{{ route('retur.history.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali ke Bulan
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="60">No</th>
                    <th>Tanggal Bon In</th>
                    <th>Total Toko</th>
                    <th>Total Retur</th>
                    <th class="text-center" width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tanggalList as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ date('d M Y', strtotime($row->tanggal_bon_in)) }}</td>
                    <td>{{ $row->total_toko }} Toko</td>
                    <td>
                        <span class="badge bg-success-subtle text-success px-3 py-1 fw-bold rounded-pill">
                            {{ $row->total_retur }} Retur
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ route('retur.history.detail', $row->tanggal_bon_in) }}" class="btn btn-sm btn-light text-primary fw-semibold" title="Lihat Detail">
                            <i class="bi bi-eye me-1"></i> Lihat
                        </a>
                        <a href="{{ route('retur.export.excel') }}" class="btn btn-sm btn-light text-success fw-semibold" title="Download Excel">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data pada bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection