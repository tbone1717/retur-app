@extends('layouts.admin')

@section('title', 'Dashboard B2B')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold text-dark m-0">Dashboard B2B</h4>
        <p class="text-muted small mb-0">Kelola B2B (SAT & MIDI).</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('b2b.input') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Input Transaksi
        </a>
        <a href="{{ route('b2b.sat') }}" class="btn btn-outline-info btn-sm px-3 shadow-sm">B2B SAT</a>
        <a href="{{ route('b2b.midi') }}" class="btn btn-outline-warning btn-sm px-3 shadow-sm">B2B MIDI</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Total Bulan Ini</span>
            <h3 class="fw-bold text-primary mb-0 mt-1">{{ $totalBulanIni }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Pending</span>
            <h3 class="fw-bold text-warning mb-0 mt-1">{{ $totalPending }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">Sudah Retur</span>
            <h3 class="fw-bold text-success mb-0 mt-1">{{ $totalSudahRetur }}</h3>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">SAT / MIDI</span>
            <h3 class="fw-bold text-dark mb-0 mt-1">{{ $totalSat }} / {{ $totalMidi }}</h3>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-clock-history text-primary me-2"></i>Transaksi B2B Terbaru</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Toko</th>
                    <th>NRB Outlet</th>
                    <th>Outlet</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($b2bTerbaru as $b2b)
                <tr>
                    <td>{{ date('d M Y', strtotime($b2b->tanggal_bon_in)) }}</td>
                    <td class="fw-bold text-dark">{{ $b2b->nama_toko }}</td>
                    <td>{{ $b2b->nrb_outlet ?? '-' }}</td>
                    <td><span class="badge bg-secondary">{{ $b2b->outlet }}</span></td>
                    <td>
                        <span class="badge bg-{{ $b2b->status == 'pending' ? 'warning' : 'success' }}">
                            {{ ucfirst($b2b->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada data B2B.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection