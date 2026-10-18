@extends('layouts.admin')

@section('title', 'Daftar Mutasi GS to BS')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark m-0">
                <i class="bi bi-arrow-repeat text-primary me-2"></i>Daftar Mutasi GS to BS (Damage Rate)
            </h5>
            <span class="text-muted small">Kelola dan unduh berita acara kerusakan produk gudang.</span>
        </div>
        <a href="{{ route('mutasi-gs-bs.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Input Mutasi Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle small">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>Nomor Mutasi</th>
                    <th>Tanggal</th>
                    <th>Depo</th>
                    <th>Minggu Ke</th>
                    <th>Keputusan</th>
                    <th>Total Item</th>
                    <th>Total Harga</th>
                    <th style="width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasis as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <!-- Nomor Mutasi Sekarang Bisa Diklik -->
                        <td class="fw-bold">
                            <a href="{{ route('mutasi-gs-bs.show', urlencode($row->nomor_mutasi)) }}" class="text-primary text-decoration-none">
                                {{ $row->nomor_mutasi }}
                            </a>
                        </td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($row->tanggal)) }}</td>
                        <td class="text-center">{{ $row->depo }}</td>
                        <td class="text-center">{{ $row->minggu_ke }}</td>
                        <td>{{ $row->keputusan_transaksi }}</td>
                        <td class="text-center">{{ $row->total_item }} SKU</td>
                        <td class="text-end fw-bold text-danger">Rp {{ number_format($row->total_harga ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('mutasi-gs-bs.show', urlencode($row->nomor_mutasi)) }}" class="btn btn-sm btn-info text-white px-2 py-1" title="Detail"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('mutasi-gs-bs.edit', urlencode($row->nomor_mutasi)) }}" class="btn btn-sm btn-warning text-white px-2 py-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                <a href="{{ route('mutasi-gs-bs.export.excel', urlencode($row->nomor_mutasi)) }}" class="btn btn-sm text-white px-2 py-1" style="background-color: #6f42c1;" title="Excel"><i class="bi bi-file-earmark-spreadsheet"></i></a>
                                <a href="{{ route('mutasi-gs-bs.export.pdf', urlencode($row->nomor_mutasi)) }}" class="btn btn-sm btn-dark px-2 py-1" title="PDF"><i class="bi bi-file-earmark-pdf"></i></a>
                                <form action="{{ route('mutasi-gs-bs.destroy', urlencode($row->nomor_mutasi)) }}" method="POST" onsubmit="return confirm('Hapus data mutasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hapus"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">Belum ada data Mutasi GS to BS.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection