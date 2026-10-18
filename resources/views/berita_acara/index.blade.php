@extends('layouts.admin')

@section('title', 'Daftar Berita Acara Pemusnahan')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <h5 class="fw-bold text-dark m-0">
                <i class="bi bi-file-earmark-text text-primary me-2"></i>Daftar Berita Acara Pemusnahan (BAP)
            </h5>
            <span class="text-muted small">Kelola dan unduh dokumen berita acara pemusnahan mandiri.</span>
        </div>
        <a href="{{ route('berita-acara.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Berita Acara Baru
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle small">
            <thead class="table-light text-center align-middle">
                <tr>
                    <th>No</th>
                    <th>Nomor BAP</th>
                    <th>Tanggal</th>
                    <th>Depo</th>
                    <th>Cara Pemusnahan</th>
                    <th style="width: 220px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($baps as $index => $row)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary">{{ $row->nomor_bap }}</td>
                        <td class="text-center">{{ date('d-m-Y', strtotime($row->tanggal)) }}</td>
                        <td class="text-center">{{ $row->depo }}</td>
                        <td>{{ $row->cara_pemusnahan }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('berita-acara.export.excel', $row->id) }}" class="btn btn-sm text-white px-2 py-1" style="background-color: #6f42c1;" title="Download Excel">
                                    <i class="bi bi-file-earmark-spreadsheet"></i> Excel
                                </a>
                                <a href="{{ route('berita-acara.export.pdf', $row->id) }}" class="btn btn-sm btn-dark px-2 py-1" title="Download PDF">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF
                                </a>
                                <form action="{{ route('berita-acara.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus BAP ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger px-2 py-1" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data Berita Acara Pemusnahan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection