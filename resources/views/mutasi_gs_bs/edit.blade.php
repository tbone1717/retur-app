@extends('layouts.admin')

@section('title', 'Edit Mutasi GS to BS')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="mb-3 pb-3 border-bottom">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Berita Acara Kerusakan Produk (Mutasi GS to BS)
        </h5>
        <span class="text-muted small">Perbarui data mutasi kerusakan produk.</span>
    </div>

    <form action="{{ route('mutasi-gs-bs.update', urlencode($header->nomor_mutasi)) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-light p-4 rounded border mb-4">
            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Informasi Dokumen & Gudang</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Nomor Dokumen</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="{{ $header->nomor_mutasi }}" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm border-primary" value="{{ $header->tanggal }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Depo</label>
                    <input type="text" name="depo" class="form-control form-control-sm border-primary" value="{{ $header->depo }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Region</label>
                    <input type="text" name="region" class="form-control form-control-sm border-primary" value="{{ $header->region }}" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Gudang Asal</label>
                    <input type="text" name="gudang_asal" class="form-control form-control-sm border-primary" value="{{ $header->gudang_asal }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Type Stock Asal</label>
                    <input type="text" name="type_stock_asal" class="form-control form-control-sm border-primary" value="{{ $header->type_stock_asal }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Gudang Tujuan</label>
                    <input type="text" name="gudang_tujuan" class="form-control form-control-sm border-primary" value="{{ $header->gudang_tujuan }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold text-primary small">Minggu Ke</label>
                    <select name="minggu_ke" class="form-select form-select-sm border-primary" required>
                        <option value="Week 1" {{ $header->minggu_ke == 'Week 1' ? 'selected' : '' }}>Week 1</option>
                        <option value="Week 2" {{ $header->minggu_ke == 'Week 2' ? 'selected' : '' }}>Week 2</option>
                        <option value="Week 3" {{ $header->minggu_ke == 'Week 3' ? 'selected' : '' }}>Week 3</option>
                        <option value="Week 4" {{ $header->minggu_ke == 'Week 4' ? 'selected' : '' }}>Week 4</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-primary small">Keputusan Transaksi</label>
                    <select name="keputusan_transaksi" class="form-select form-select-sm border-primary" required>
                        <option value="Dilakukan mutasi Internal Depo" {{ $header->keputusan_transaksi == 'Dilakukan mutasi Internal Depo' ? 'selected' : '' }}>Dilakukan mutasi Internal Depo</option>
                        <option value="Dilakukan Pembebanan" {{ $header->keputusan_transaksi == 'Dilakukan Pembebanan' ? 'selected' : '' }}>Dilakukan Pembebanan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- TABEL ITEM PRODUK -->
        <div class="table-responsive mb-3 border rounded">
            <table class="table table-bordered table-sm align-middle m-0" id="mutasiTable">
                <thead class="table-light text-center small align-middle">
                    <tr>
                        <th style="min-width: 160px;">Kode SKU</th>
                        <th style="min-width: 60px;">ED</th>
                        <th style="min-width: 60px;">HM</th>
                        <th style="min-width: 60px;">KR</th>
                        <th style="min-width: 60px;">NS</th>
                        <th style="min-width: 60px;">LL</th>
                        <th style="min-width: 60px;">NED</th>
                        <th style="min-width: 70px;">Tot PCS</th>
                        <th style="min-width: 130px;">Jumlah Harga (Rp)</th>
                        <th style="min-width: 180px;">Keterangan</th>
                        <th style="width: 50px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach($items as $item)
                        @php
                            $prod = DB::table('produks')->where('sku', $item->kode_produk)->first();
                            $hargaSat = $prod ? ($prod->harga ?? $prod->price ?? $prod->hargajual ?? 0) : 0;
                        @endphp
                        <tr>
                            <td><input type="text" list="listProduk" name="kode_produk[]" class="form-control form-control-sm sku-input" value="{{ $item->kode_produk }}" required autocomplete="off"></td>
                            <td><input type="number" name="qty_ed[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ed }}"></td>
                            <td><input type="number" name="qty_hm[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_hm }}"></td>
                            <td><input type="number" name="qty_kr[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_kr }}"></td>
                            <td><input type="number" name="qty_ns[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ns }}"></td>
                            <td><input type="number" name="qty_ll[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ll }}"></td>
                            <td><input type="number" name="qty_ned[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ned }}"></td>
                            <td><input type="text" class="form-control form-control-sm total-pcs text-center fw-bold text-primary bg-light" readonly value="{{ $item->total_pcs }}"></td>
                            <td>
                                <input type="text" class="form-control form-control-sm total-harga-display text-end fw-bold text-danger bg-light" readonly value="Rp {{ number_format($item->jumlah_harga, 0, ',', '.') }}">
                            </td>
                            <td><input type="text" name="keterangan[]" class="form-control form-control-sm shortcut-keterangan" value="{{ $item->keterangan }}"></td>
                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex gap-2 mt-4 pt-3 border-top">
            <button type="button" class="btn btn-sm btn-success px-3" id="addRowBtn">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris
            </button>
            <a href="{{ route('mutasi-gs-bs.index') }}" class="btn btn-sm btn-secondary px-3">Batal</a>
            <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>

        <datalist id="listProduk">
            @foreach($produk as $p)
                @php $hVal = $p->harga ?? $p->price ?? $p->hargajual ?? 0; @endphp
                <option value="{{ $p->sku }}" data-harga="{{ $hVal }}">{{ $p->keterangan ?? $p->nama_produk ?? '' }}</option>
            @endforeach
        </datalist>
    </form>
</div>

<script>
    const produkMap = {
        @foreach($produk as $p)
            @php $hVal = $p->harga ?? $p->price ?? $p->hargajual ?? 0; @endphp
            "{{ strtoupper($p->sku) }}": { harga: {{ $hVal }} },
        @endforeach
    };

    function formatRupiah(angka) { return 'Rp ' + Number(angka).toLocaleString('id-ID'); }

    function calculateRow(tr) {
        let sku = tr.querySelector('.sku-input').value.trim().toUpperCase();
        let qtyInputs = tr.querySelectorAll('.qty-input');
        let totalPcs = 0;
        qtyInputs.forEach(input => { totalPcs += (parseInt(input.value) || 0); });
        let hargaSatuan = produkMap[sku] ? produkMap[sku].harga : 0;
        let totalHarga = totalPcs * hargaSatuan;
        tr.querySelector('.total-pcs').value = totalPcs;
        tr.querySelector('.total-harga-display').value = formatRupiah(totalHarga);
    }

    document.getElementById('tableBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('sku-input')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    document.getElementById('addRowBtn').addEventListener('click', function() {
        let tbody = document.getElementById('tableBody');
        let newRow = tbody.querySelector('tr').cloneNode(true);
        newRow.querySelectorAll('input').forEach(inp => inp.value = inp.classList.contains('qty-input') ? 0 : '');
        newRow.querySelector('.total-pcs').value = 0;
        newRow.querySelector('.total-harga-display').value = 'Rp 0';
        tbody.appendChild(newRow);
        newRow.querySelector('.sku-input').focus();
    });

    document.getElementById('tableBody').addEventListener('click', function(e) {
        let btn = e.target.closest('.remove-row');
        if (btn && document.querySelectorAll('#tableBody tr').length > 1) {
            btn.closest('tr').remove();
        }
    });
</script>
@endsection