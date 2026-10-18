@extends('layouts.admin')

@section('title', 'Data Karung')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-box-seam text-primary me-2"></i>Daftar Data Karung
        </h5>
        
        <!-- Form Pencarian & Tombol Tambah -->
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form action="{{ route('karung.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari No Karung / Jenis..." value="{{ request('cari') }}" style="width: 220px;">
                <button type="submit" class="btn btn-outline-primary btn-sm px-3">Cari</button>
                @if(request('cari'))
                    <a href="{{ route('karung.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset Pencarian">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </form>
            <a href="{{ route('karung.input') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3">
                <i class="bi bi-plus-lg"></i> Tambah Karung
            </a>
        </div>
    </div>
    <hr class="mb-4 text-muted">

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2 small mb-4" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="padding: 0.75rem 1rem;"></button>
        </div>
    @endif

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
                    <th width="15%">Tanggal</th>
                    <th width="20%">No Karung</th>
                    <th width="15%">Jenis Produk</th>
                    <th width="15%">Status</th>
                    <th width="20%">Total Isi</th>
                    <th width="10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($karungs as $index => $karung)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ date('d M Y', strtotime($karung->tanggal)) }}</td>
                    <td>
                        <a href="javascript:void(0)" 
                           class="fw-bold text-primary text-decoration-none view-karung" 
                           data-bs-toggle="modal" 
                           data-bs-target="#detailKarungModal" 
                           data-karung='@json($karung)'
                           title="Klik untuk lihat detail">
                           {{ $karung->no_karung }}
                        </a>
                    </td>
                    <td class="fw-semibold">{{ $karung->jenis_produk }}</td>
                    <td>
                        @php
                            $badgeColor = 'bg-secondary';
                            if($karung->status == 'Reguler') $badgeColor = 'bg-success';
                            elseif($karung->status == 'Prochiz') $badgeColor = 'bg-warning text-dark';
                            elseif($karung->status == 'NPL') $badgeColor = 'bg-danger';
                            elseif($karung->status == 'Skippy') $badgeColor = 'bg-info text-dark';
                            elseif($karung->status == 'Seasonal') $badgeColor = 'bg-primary';
                        @endphp
                        <span class="badge {{ $badgeColor }} px-2 py-1">{{ $karung->status }}</span>
                    </td>
                    <td>
                        <span class="fw-semibold text-dark">{{ $karung->items->count() }} SKU</span> 
                        <span class="text-muted small">({{ $karung->items->sum('qty') }} Pcs)</span>
                    </td>
                    <!-- Di bagian kolom aksi tabel karung -->
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2 align-items-center">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary view-karung" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailKarungModal" 
                                    data-karung='@json($karung)'
                                    title="Detail Karung">
                                <i class="bi bi-eye"></i>
                            </button>

                            @if($karung->pemusnahan)
                                <!-- Jika sudah diajukan pemusnahan, tombol edit dikunci -->
                                <span class="badge bg-secondary px-2 py-1" title="Karung sedang dalam pengajuan pemusnahan (Terkunci)">Terkunci</span>
                            @else
                                <a href="{{ route('karung.edit', $karung->id) }}" class="btn btn-sm btn-outline-warning" title="Edit Karung">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Tidak ada data karung yang ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detail Karung -->
<div class="modal fade" id="detailKarungModal" tabindex="-1" aria-labelledby="detailKarungModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold text-dark" id="detailKarungModalLabel">
                    <i class="bi bi-box-seam text-primary me-2"></i>Detail Karung: <span id="modalNoKarung" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <span class="text-muted small d-block">Tanggal Pembuatan</span>
                        <strong id="modalTanggal" class="text-dark"></strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block">Jenis Produk</span>
                        <strong id="modalJenis" class="text-dark"></strong>
                    </div>
                    <div class="col-md-4">
                        <span class="text-muted small d-block">Status Karung</span>
                        <strong id="modalStatus" class="text-dark"></strong>
                    </div>
                </div>

                <h6 class="fw-bold text-dark mb-3">Daftar Item / SKU dalam Karung</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="modalItemsTable">
                        <thead class="table-light">
                            <tr>
                                <th width="8%" class="text-center">No</th>
                                <th width="32%">SKU</th>
                                <th width="20%">Qty (Pcs)</th>
                                <th width="40%">Keterangan / Nama Produk</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Diisi via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary px-4 btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detailModal = document.getElementById('detailKarungModal');
        detailModal.addEventListener('show.bs.modal', function (event) {
            let button = event.relatedTarget;
            let karung = JSON.parse(button.getAttribute('data-karung'));

            // Set Header Info
            document.getElementById('modalNoKarung').innerText = karung.no_karung;
            document.getElementById('modalTanggal').innerText = new Date(karung.tanggal).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            document.getElementById('modalJenis').innerText = karung.jenis_produk;
            document.getElementById('modalStatus').innerText = karung.status;

            // Render Items Table inside Modal
            let tbody = document.querySelector('#modalItemsTable tbody');
            tbody.innerHTML = '';

            if (karung.items && karung.items.length > 0) {
                karung.items.forEach((item, index) => {
                    let ket = item.produk ? (item.produk.keterangan || item.produk.nama_produk || '-') : '-';
                    let tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center">${index + 1}</td>
                        <td class="fw-bold">${item.sku}</td>
                        <td>${item.qty} Pcs</td>
                        <td>${ket}</td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                let tr = document.createElement('tr');
                tr.innerHTML = `<td colspan="4" class="text-center text-muted py-3">Tidak ada item dalam karung ini.</td>`;
                tbody.appendChild(tr);
            }
        });
    });
</script>
@endsection