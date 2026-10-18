@extends('layouts.admin')

@section('title', 'Data Pemusnahan Karung')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-trash3 text-danger me-2"></i>Daftar Pengajuan Pemusnahan Karung
        </h5>
        
        <!-- Tombol Cetak PDF & Ajukan Baru -->
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form action="{{ route('pemusnahan.pdf') }}" method="GET" target="_blank" class="d-flex gap-2">
                <select name="tahap" class="form-select form-select-sm" style="width: 130px;">
                    <option value="">Semua Tahap</option>
                    <option value="1">Tahap 1</option>
                    <option value="2">Tahap 2</option>
                    <option value="3">Tahap 3</option>
                    <option value="4">Tahap 4</option>
                </select>
                <button type="submit" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
                    <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                </button>
            </form>
            <a href="{{ route('pemusnahan.input') }}" class="btn btn-danger btn-sm d-flex align-items-center gap-1 px-3">
                <i class="bi bi-plus-lg"></i> Ajukan Pemusnahan Baru
            </a>
        </div>
    </div>
    <hr class="mb-4 text-muted">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 small mb-4" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%" class="text-center">No</th>
                    <th width="15%">Tanggal Pengajuan</th>
                    <th width="25%">No Karung</th>
                    <th width="15%">Tahap</th>
                    <th width="25%">Jenis Produk</th>
                    <th width="15%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemusnahans as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ date('d M Y', strtotime($p->tanggal_pengajuan)) }}</td>
                    <td class="fw-bold text-danger">{{ $p->karung->no_karung ?? '-' }}</td>
                    <td>
                        <span class="badge bg-dark px-2 py-1">Tahap {{ $p->tahap }}</span>
                    </td>
                    <td class="fw-semibold">{{ $p->karung->jenis_produk ?? '-' }}</td>
                    <td class="text-center">
                        <form action="{{ route('pemusnahan.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan ini? Karung akan kembali tersedia untuk diajukan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Batalkan / Hapus">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan pemusnahan karung.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection