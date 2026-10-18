@extends('layouts.admin')

@section('title', 'Data Produk')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <!-- Form Pencarian SKU -->
        <form action="{{ route('produk.index') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari berdasarkan SKU..." style="width: 250px;" value="{{ request('cari') }}">
            <button type="submit" class="btn btn-outline-secondary btn-sm">Cari</button>
        </form>

        <!-- Kumpulan Tombol Aksi Kanan -->
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('produk.export') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-download me-1"></i> Download
            </a>

            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bi bi-file-earmark-excel me-1"></i> Import Excel
            </button>

            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahProdukModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah Produk
            </button>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="50">No</th>
                    <th>SKU</th>
                    <th>Keterangan</th>
                    <th>UoM (Dus/Bal)</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Barcode</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produks ?? [] as $index => $p)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="fw-semibold">{{ $p->sku }}</td>
                    <td>{{ $p->keterangan }}</td>
                    <td>{{ $p->uom ?? 1 }}</td>
                    <td>Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                    <td>
                        @php
                            $badgeClass = 'bg-secondary text-white';
                            $statusUpper = strtoupper($p->status);

                            if (str_contains($statusUpper, 'EXISTING')) {
                                $badgeClass = 'bg-success text-white';
                            } elseif (str_contains($statusUpper, 'NPL')) {
                                $badgeClass = 'bg-danger text-white';
                            } elseif (str_contains($statusUpper, 'PROCHIZ')) {
                                $badgeClass = 'bg-warning text-dark';
                            } elseif (str_contains($statusUpper, 'SEASONAL') || str_contains($statusUpper, 'SUSU') || str_contains($statusUpper, 'JMK')) {
                                $badgeClass = 'bg-secondary text-white';
                            } elseif (str_contains($statusUpper, 'QUAKER OAT')) {
                                $badgeClass = 'bg-info text-dark';
                            } elseif (str_contains($statusUpper, 'SKIPPY')) {
                                $badgeClass = 'bg-primary text-white';
                            }
                        @endphp

                        <span class="badge {{ $badgeClass }} px-2 py-1 fw-bold">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td><code>{{ $p->barcode ?? '-' }}</code></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#editProdukModal{{ $p->id }}" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </button>

                        <form action="{{ route('produk.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light text-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data produk tersedia.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= MODAL-MODAL DI LUAR TABEL ================= -->

<!-- Loop Modal Edit Produk untuk setiap baris -->
@foreach($produks ?? [] as $p)
<div class="modal fade" id="editProdukModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('produk.update', $p->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Produk: {{ $p->sku }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" value="{{ $p->sku }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan / Nama Produk</label>
                    <textarea name="keterangan" class="form-control" rows="2">{{ $p->keterangan }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="uom" class="form-label">UoM (Dus/Bal) / Isi per Karton</label>
                    <input type="number" step="0.01" class="form-control" id="uom" name="uom" value="{{ old('uom', $p->uom ?? 1) }}" required>
                    <small class="text-muted">Masukkan jumlah pcs dalam 1 Dus/Bal (contoh: 24, 48, dll.)</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" value="{{ $p->harga }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="EXISTING" {{ $p->status == 'EXISTING' ? 'selected' : '' }}>EXISTING</option>
                        <option value="EXISTING (GPPJ)" {{ $p->status == 'EXISTING (GPPJ)' ? 'selected' : '' }}>EXISTING (GPPJ)</option>
                        <option value="EXISTING (GBS)" {{ $p->status == 'EXISTING (GBS)' ? 'selected' : '' }}>EXISTING (GBS)</option>
                        <option value="EXISTING ( RUMUT )" {{ $p->status == 'EXISTING ( RUMUT )' ? 'selected' : '' }}>EXISTING ( RUMUT )</option>
                        <option value="NPL (BAULI)" {{ $p->status == 'NPL (BAULI)' ? 'selected' : '' }}>NPL (BAULI)</option>
                        <option value="NPL (GPPJ)" {{ $p->status == 'NPL (GPPJ)' ? 'selected' : '' }}>NPL (GPPJ)</option>
                        <option value="NPL ( GBS )" {{ $p->status == 'NPL ( GBS )' ? 'selected' : '' }}>NPL ( GBS )</option>
                        <option value="PROCHIZ" {{ $p->status == 'PROCHIZ' ? 'selected' : '' }}>PROCHIZ</option>
                        <option value="SEASONAL" {{ $p->status == 'SEASONAL' ? 'selected' : '' }}>SEASONAL</option>
                        <option value="SUSU" {{ $p->status == 'SUSU' ? 'selected' : '' }}>SUSU</option>
                        <option value="JMK" {{ $p->status == 'JMK' ? 'selected' : '' }}>JMK</option>
                        <option value="QUAKER OAT" {{ $p->status == 'QUAKER OAT' ? 'selected' : '' }}>QUAKER OAT</option>
                        <option value="SKIPPY" {{ $p->status == 'SKIPPY' ? 'selected' : '' }}>SKIPPY</option>
                        <option value="DEL" {{ $p->status == 'DEL' ? 'selected' : '' }}>DEL</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" class="form-control" value="{{ $p->barcode }}">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('produk.import') }}" method="POST" enctype="multipart/form-data" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Import Data Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Pilih File Excel (.xlsx / .xls)</label>
                    <input type="file" name="file_excel" class="form-control" required accept=".xlsx, .xls, .csv">
                    <div class="form-text">Pastikan file memiliki kolom SKU, Keterangan, Harga, Status, dan Barcode.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success btn-sm">Unggah & Import</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Produk -->
<div class="modal fade" id="tambahProdukModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('produk.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label">SKU</label>
                    <input type="text" name="sku" class="form-control" required placeholder="Kode SKU...">
                </div>
                <div class="mb-3">
                    <label class="form-label">Keterangan / Nama Produk</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Deskripsi produk..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">UoM (Dus/Bal) / Isi per Karton</label>
                    <input type="number" step="0.01" name="uom" class="form-control" value="1" required placeholder="1">
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="harga" class="form-control" required placeholder="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="" disabled selected>Pilih Status Produk...</option>
                        <option value="EXISTING">EXISTING</option>
                        <option value="EXISTING (GPPJ)">EXISTING (GPPJ)</option>
                        <option value="EXISTING (GBS)">EXISTING (GBS)</option>
                        <option value="EXISTING ( RUMUT )">EXISTING ( RUMUT )</option>
                        <option value="NPL (BAULI)">NPL (BAULI)</option>
                        <option value="NPL (GPPJ)">NPL (GPPJ)</option>
                        <option value="NPL ( GBS )">NPL ( GBS )</option>
                        <option value="PROCHIZ">PROCHIZ</option>
                        <option value="SEASONAL">SEASONAL</option>
                        <option value="SUSU">SUSU</option>
                        <option value="JMK">JMK</option>
                        <option value="QUAKER OAT">QUAKER OAT</option>
                        <option value="SKIPPY">SKIPPY</option>
                        <option value="DEL">DEL</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Barcode</label>
                    <input type="text" name="barcode" class="form-control" placeholder="Nomor barcode...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
@endsection