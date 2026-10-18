@extends('layouts.admin')

@section('title', 'Detail Retur Tanggal')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-dark mb-1"><i class="bi bi-list-check text-primary me-2"></i>Detail Retur Tanggal: {{ date('d M Y', strtotime($tanggal)) }}</h5>
            <p class="text-muted small mb-0">Total Toko Berpartisipasi: <strong class="text-dark">{{ $totalToko }} Toko</strong></p>
        </div>
        
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form action="{{ route('retur.history.detail', $tanggal) }}" method="GET" class="d-flex gap-2">
                <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari nama toko..." value="{{ request('cari') }}">
                <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
            </form>
            <a href="{{ route('retur.history.tanggal', date('Y-m', strtotime($tanggal))) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>Nama Toko</th>
                    <th>NRB Outlet</th>
                    <th>Driver</th>
                    <th>Total SKU</th>
                    <th class="text-center" width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($returs as $index => $r)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-semibold text-dark">{{ $r->nama_toko }}</td>
                    <td>{{ $r->nrb_outlet ?? '-' }}</td>
                    <td>{{ $r->driver }}</td>
                    <td>
                        <span class="badge bg-primary-subtle text-primary px-3 py-1 fw-bold rounded-pill">
                            {{ $r->items->count() }} SKU
                        </span>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light text-info" data-bs-toggle="modal" data-bs-target="#detailReturModal{{ $r->id }}" title="Detail">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                        <a href="{{ route('retur.edit', $r->id) }}" class="btn btn-sm btn-light text-primary" title="Edit">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <form action="{{ route('retur.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus transaksi retur toko ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Tidak ada data retur ditemukan pada tanggal ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL DETAIL DI LUAR TABEL ================= -->
@foreach($returs as $r)
<div class="modal fade" id="detailReturModal{{ $r->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Rincian Retur: {{ $r->nama_toko }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="row mb-3 small bg-light p-3 rounded-3">
                    <div class="col-md-4"><strong>Tanggal Bon In:</strong> {{ $r->tanggal_bon_in }}</div>
                    <div class="col-md-4"><strong>Driver:</strong> {{ $r->driver }}</div>
                    <div class="col-md-4"><strong>NRB Outlet:</strong> {{ $r->nrb_outlet ?? '-' }}</div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>SKU</th>
                                <th>Qty</th>
                                <th>Status</th>
                                <th>Barcode</th>
                                <th>Sum Price</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($r->items as $item)
                            <tr>
                                <td class="fw-semibold">{{ $item->sku }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->status ?? '-' }}</td>
                                <td><code class="font-monospace">{{ $item->barcode ?? '-' }}</code></td>
                                <td>Rp {{ number_format($item->sum_price, 0, ',', '.') }}</td>
                                <td>{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="4" class="text-end">Grand Total:</td>
                                <td colspan="2">Rp {{ number_format($r->items->sum('sum_price'), 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection