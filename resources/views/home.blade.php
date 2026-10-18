@extends('layouts.admin')

@section('title', 'Dashboard Utama')

@section('content')

<!-- Header Bagian Atas -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold m-0" style="color: var(--text-main);">
            Selamat datang, {{ Auth::user()->name }} 👋
        </h3>
        <span style="color: var(--text-muted); font-size: 0.9rem;">
            Depo: <strong>{{ Auth::user()->depo }}</strong> — Ringkasan Aktivitas Retur.
        </span>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('retur.input') }}" class="btn btn-primary btn-sm fw-semibold px-3 rounded-pill">
            <i class="bi bi-plus-circle me-1"></i> Buat Retur Baru
        </a>
        <a href="{{ route('produk.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold px-3 rounded-pill">
            <i class="bi bi-box-seam me-1"></i> Data Produk
        </a>
    </div>
</div>

<!-- Statistik Cards (Dinamis dari Controller) -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-bold">BULAN INI</span>
            <h2 class="fw-bold my-2 text-primary">{{ $currentMonthCount }}</h2>
            <span class="small text-success"><i class="bi bi-arrow-up-short"></i> {{ $formattedPercent }}</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-bold">MINGGU INI</span>
            <h2 class="fw-bold my-2 text-success">{{ $totalMingguIni }}</h2>
            <span class="small text-muted">Transaksi aktif</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-bold">HARI INI</span>
            <h2 class="fw-bold my-2 text-warning">{{ $totalHariIni }}</h2>
            <span class="small text-muted">Transaksi masuk</span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card p-3 border-0 shadow-sm h-100">
            <span class="text-muted small fw-bold">TOTAL KARUNG</span>
            <h2 class="fw-bold my-2 text-info">{{ $totalKarung }}</h2>
            <span class="small text-muted">Akumulasi Sistem</span>
        </div>
    </div>
</div>

<!-- Tabel Retur Terbaru (Dinamis dari Database) -->
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0" style="color: var(--text-main);">
            <i class="bi bi-clock-history text-primary me-2"></i>Retur Terbaru
        </h5>
        <a href="{{ route('retur.history.index') }}" class="text-decoration-none small fw-semibold text-primary">Lihat Semua</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle small text-nowrap">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Toko</th>
                    <th>Driver</th>
                    <th>Total SKU</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returTerbaru as $item)
                    <tr>
                        <td>{{ $item->tanggal_bon_in }}</td>
                        <td>{{ $item->nama_toko ?? '-' }}</td>
                        <td>{{ $item->driver ?? '-' }}</td>
                        <td>{{ $item->items->count() ?? 0 }}</td>
                        <td class="text-center">
                            <a href="{{ route('retur.history.detail', $item->tanggal_bon_in) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada transaksi retur tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection