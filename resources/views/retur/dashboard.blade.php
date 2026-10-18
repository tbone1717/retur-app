@extends('layouts.admin')

@section('title', 'Dashboard Retur BS')

@section('content')
<!-- Tombol Aksi Cepat -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <p class="text-muted small mb-0">Kelola dan pantau transaksi retur barang rusak (BS) outlet.</p>
    <div class="d-flex gap-2">
        <a href="{{ route('retur.input') }}" class="btn btn-primary btn-sm px-3 shadow-sm d-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Input Retur Baru
        </a>
        <a href="{{ route('retur.history.index') }}" class="btn btn-outline-secondary btn-sm px-3 bg-white shadow-sm d-flex align-items-center gap-1">
            <i class="bi bi-clock-history"></i> History Retur
        </a>
    </div>
</div>

<style>
    .card {
        border-radius: 1rem !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 1.25rem;
    }
</style>

<!-- Header & Form Pencarian Histori -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold m-0" style="color: var(--text-main);">Daftar Histori Retur</h5>
    <form action="{{ route('retur.dashboard') }}" method="GET" class="d-flex gap-2">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Toko, Driver, NRB..." value="{{ request('search') }}" style="width: 250px;">
        <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
        @if(request('search'))
            <a href="{{ route('retur.dashboard') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        @endif
    </form>
</div>

<!-- Statistik Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Bulan Ini</span>
                    <h3 class="fw-bold text-primary mb-0 mt-1">{{ $totalBulanIni }}</h3>
                    <span class="text-muted small">Total transaksi bulan ini</span>
                </div>
                <div class="rounded-circle bg-primary bg-opacity-10 p-3 text-primary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 3px;"></div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Minggu Ini</span>
                    <h3 class="fw-bold text-success mb-0 mt-1">{{ $totalMingguIni }}</h3>
                    <span class="text-muted small">Total transaksi minggu ini</span>
                </div>
                <div class="rounded-circle bg-success bg-opacity-10 p-3 text-success d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-graph-up-arrow fs-4"></i>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-success" style="height: 3px;"></div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 position-relative overflow-hidden">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">Hari Ini</span>
                    <h3 class="fw-bold text-warning mb-0 mt-1">{{ $totalHariIni }}</h3>
                    <span class="text-muted small">Total transaksi hari ini</span>
                </div>
                <div class="rounded-circle bg-warning bg-opacity-10 p-3 text-warning d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                    <i class="bi bi-receipt fs-4"></i>
                </div>
            </div>
            <div class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 3px;"></div>
        </div>
    </div>
</div>

<!-- Grid Tabel & SKU Terbanyak -->
<div class="row g-4">
    <!-- Tabel Retur Terbaru dengan Pagination -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-dark m-0"><i class="bi bi-clock-history text-primary me-2"></i>Retur Terbaru</h5>
                    <a href="{{ route('retur.history.index') }}" class="btn btn-sm btn-light text-primary fw-semibold">Lihat Semua</a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light rounded">
                            <tr>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Nama Toko</th>
                                <th class="py-3">Driver</th>
                                <th class="py-3">Total SKU</th>
                                <th class="py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($returTerbaru as $retur)
                            <tr>
                                <td class="fw-semibold text-secondary">{{ date('d M Y', strtotime($retur->tanggal_bon_in)) }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $retur->nama_toko }}</div>
                                    <small class="text-muted">B2B: {{ $retur->b2b ?? '-' }}</small>
                                </td>
                                <td>{{ $retur->driver }}</td>
                                <td><span class="badge bg-primary-subtle text-primary px-2 py-1 fw-bold">{{ $retur->items->count() }} SKU</span></td>
                                <td class="text-center">
                                    <a href="{{ route('retur.history.detail', $retur->tanggal_bon_in) }}" class="btn btn-sm btn-light text-info" title="Lihat Detail"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Belum ada data transaksi retur.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Navigasi Paging / Halaman (Aman untuk 1,000+ Data) -->
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mt-3 pt-3 border-top gap-2">
                <span class="text-muted small">
                    Menampilkan {{ $returTerbaru->firstItem() ?? 0 }} - {{ $returTerbaru->lastItem() ?? 0 }} dari total {{ $returTerbaru->total() }} data
                </span>
                <div>
                    {{ $returTerbaru->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- SKU Terbanyak -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-trophy text-warning me-2"></i>SKU Terbanyak di Retur</h5>
            <p class="text-muted small mb-3">3 produk dengan total kuantitas retur tertinggi.</p>

            <div class="d-flex flex-column gap-3">
                @forelse($skuTerbanyak as $index => $item)
                <div class="p-3 rounded-3 bg-light border border-light d-flex align-items-center justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary rounded-circle" style="width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem;">{{ $index + 1 }}</span>
                            <span class="fw-bold text-dark">{{ $item->sku }}</span>
                        </div>
                        <span class="text-muted small">Total Qty: <strong class="text-danger">{{ $item->total_qty }}</strong> pcs</span>
                    </div>
                    <div class="text-end">
                        <span class="d-block fw-bold text-dark small">Rp {{ number_format($item->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 small">Belum ada data produk retur.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection