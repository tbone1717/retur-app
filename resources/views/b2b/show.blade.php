@extends('layouts.admin')

@section('title', 'Detail B2B')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-info-circle text-primary me-2"></i>Detail Transaksi B2B {{ $b2b->nama_toko }}
        </h5>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <hr class="mb-4 text-muted">

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <span class="text-muted small d-block">Tanggal Bon In</span>
            <strong class="text-dark">{{ date('d M Y', strtotime($b2b->tanggal_bon_in)) }}</strong>
        </div>
        <div class="col-md-3">
            <span class="text-muted small d-block">Nama Toko</span>
            <strong class="text-dark">{{ $b2b->nama_toko }}</strong>
        </div>
        <div class="col-md-3">
            <span class="text-muted small d-block">NRB Outlet</span>
            <strong class="text-primary">{{ $b2b->nrb_outlet ?? '-' }}</strong>
        </div>
        <div class="col-md-3">
            <span class="text-muted small d-block">Outlet</span>
            <span class="badge bg-secondary">{{ $b2b->outlet }}</span>
        </div>
        <div class="col-md-3">
            <span class="text-muted small d-block">Status</span>
            <span class="badge bg-{{ $b2b->status == 'pending' ? 'warning text-dark' : 'success' }}">{{ ucfirst($b2b->status) }}</span>
        </div>
    </div>

    <h6 class="fw-bold text-dark mb-3">Daftar Item SKU Produk</h6>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>SKU</th>
                    <th class="text-center">Qty</th>
                    <th>Warning</th>
                    <th>Sum Price</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($b2b->items as $item)
                <tr>
                    <td class="fw-bold">{{ $item->sku }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td>
                        @if($item->warning)
                            <span class="text-danger fw-semibold">{{ $item->warning }}</span>
                        @else
                            <span class="text-success">Aman</span>
                        @endif
                    </td>
                    <td>Rp {{ number_format($item->sum_price, 0, ',', '.') }}</td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection