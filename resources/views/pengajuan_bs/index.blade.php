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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle small">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th width="3%">No</th>
                    <th>No. Pengajuan</th>
                    <th width="15%">Hari / Tanggal</th>
                    <th>Periode</th>
                    <th>Depo Asal</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Item SKU</th>
                    <th>Total Harga</th>
                    <th width="16%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengajuans as $index => $item)
                    @php
                        $days = ['Sun' => 'Minggu', 'Mon' => 'Senin', 'Tue' => 'Selasa', 'Wed' => 'Rabu', 'Thu' => 'Kamis', 'Fri' => 'Jumat', 'Sat' => 'Sabtu'];
                        $engDay = $item->tanggal_pengajuan ? date('D', strtotime($item->tanggal_pengajuan)) : '';
                        $indoDay = $days[$engDay] ?? '';
                        $formattedDate = $item->tanggal_pengajuan ? $indoDay . ', ' . date('d-m-Y', strtotime($item->tanggal_pengajuan)) : '-';
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-center">
                            @if(!empty($item->nomor_pengajuan))
                                <a href="{{ route('pengajuan-bs.show', ['nomor_pengajuan' => $item->nomor_pengajuan]) }}" class="text-primary text-decoration-none">
                                    {{ $item->nomor_pengajuan }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $formattedDate }}</td>
                        <td class="text-center fw-bold">{{ $item->periode ?? '-' }}</td>
                        <td class="text-center">{{ $item->depo_asal ?? 'Bekasi Utama' }}</td>
                        <td class="text-center">{{ $item->jenis_pemusnahan ?? '-' }}</td>
                        <td class="text-center">{{ $item->status_produk ?? '-' }}</td>
                        <td class="text-center text-secondary fw-bold">{{ $item->total_item }} SKU</td>
                        <td class="text-end fw-bold text-danger">
                            Rp {{ number_format($item->total_harga ?? 0, 0, ',', '.') }}
                        </td>
                        
                        <td class="text-center">
                            @if(!empty($item->nomor_pengajuan))
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('pengajuan-bs.edit', $item->nomor_pengajuan) }}" class="btn btn-sm btn-primary" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <a href="{{ route('pengajuan-bs.export.excel', ['nomor_pengajuan' => $item->nomor_pengajuan]) }}" class="btn btn-sm btn-success" title="Download Excel">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                    <a href="{{ route('pengajuan-bs.export.pdf', ['nomor_pengajuan' => $item->nomor_pengajuan]) }}" class="btn btn-sm btn-warning text-white" title="Download PDF" target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                    <form action="{{ route('pengajuan-bs.destroy', ['nomor_pengajuan' => $item->nomor_pengajuan]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengajuan nomor {{ $item->nomor_pengajuan }} ini?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
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