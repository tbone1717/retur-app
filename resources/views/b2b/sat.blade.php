@extends('layouts.admin')

@section('title', 'Data B2B - ' . $outlet)

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h5 class="fw-bold text-dark m-0">Daftar Transaksi B2B {{ $outlet }}</h5>
        <form action="" method="GET" class="d-flex gap-2">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari toko / NRB..." value="{{ request('cari') }}">
            <button type="submit" class="btn btn-primary btn-sm px-3">Cari</button>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Toko</th>
                    <th>NRB Outlet</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($b2bs as $b2b)
                <tr>
                    <td>{{ date('d M Y', strtotime($b2b->tanggal_bon_in)) }}</td>
                    <td class="fw-bold text-dark">{{ $b2b->nama_toko }}</td>
                    <td>
                        <a href="{{ route('b2b.show', $b2b->id) }}" class="text-primary text-decoration-none fw-semibold" title="Klik untuk lihat detail">
                            {{ $b2b->nrb_outlet ?? '-' }} <i class="bi bi-box-arrow-up-right small ms-1"></i>
                        </a>
                    </td>
                    <td>
                        <span class="badge bg-{{ $b2b->status == 'pending' ? 'warning text-dark' : 'success' }}">
                            {{ ucfirst($b2b->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($b2b->status !== 'sudah retur')
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('b2b.edit', $b2b->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Transaksi">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('b2b.destroy', $b2b->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Transaksi">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="badge bg-light text-muted border px-2 py-1 small">Terkunci (Sudah Retur)</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Belum ada B2B {{ $outlet }}.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-3">
        {{ $b2bs->withQueryString()->links() }}
    </div>
</div>
@endsection