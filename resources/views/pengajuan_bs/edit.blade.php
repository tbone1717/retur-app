@extends('layouts.admin')

@section('title', 'Edit Pengajuan BS Reguler')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="mb-3 pb-3 border-bottom">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Pengajuan Pemusnahan BS: {{ $header->nomor_pengajuan }}
        </h5>
    </div>

    <form action="{{ route('pengajuan-bs.update', urlencode($header->nomor_pengajuan)) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- HEADER PENGAJUAN -->
        <div class="bg-light p-4 rounded border mb-4">
            <h6 class="fw-bold text-secondary mb-3 border-bottom pb-2">Informasi Pengajuan</h6>
            <div class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Nomor Pengajuan</label>
                    <input type="text" name="nomor_pengajuan" class="form-control form-control-sm fw-bold border-primary" value="{{ $header->nomor_pengajuan }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" class="form-control form-control-sm border-primary" value="{{ $header->tanggal_pengajuan }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Periode</label>
                    <input type="text" name="periode" class="form-control form-control-sm border-primary" value="{{ $header->periode }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Depo Asal</label>
                    <input type="text" name="depo_asal" class="form-control form-control-sm bg-white border-primary" value="{{ $header->depo_asal }}" required readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Jenis Pemusnahan</label>
                    <select name="jenis_pemusnahan" class="form-select form-select-sm border-primary" required>
                        <option value="Barang Rusak (BS)" {{ $header->jenis_pemusnahan == 'Barang Rusak (BS)' ? 'selected' : '' }}>Barang Rusak (BS)</option>
                        <option value="Expired Date (ED)" {{ $header->jenis_pemusnahan == 'Expired Date (ED)' ? 'selected' : '' }}>Expired Date (ED)</option>
                        <option value="Biaya SNS / Klaim Principal" {{ $header->jenis_pemusnahan == 'Biaya SNS / Klaim Principal' ? 'selected' : '' }}>Biaya SNS / Klaim Principal</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold text-primary small">Status Produk</label>
                    <select name="status_produk" class="form-select form-select-sm border-primary" required>
                        <option value="">-- Pilih Status --</option>
                        @if(isset($statuses) && count($statuses) > 0)
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ $header->status_produk == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        @else
                            <option value="Reguler" {{ $header->status_produk == 'Reguler' ? 'selected' : '' }}>Reguler</option>
                            <option value="Prochiz" {{ $header->status_produk == 'Prochiz' ? 'selected' : '' }}>Prochiz</option>
                            <option value="Skippy" {{ $header->status_produk == 'Skippy' ? 'selected' : '' }}>Skippy</option>
                            <option value="Quaker" {{ $header->status_produk == 'Quaker' ? 'selected' : '' }}>Quaker</option>
                            <option value="NPL" {{ $header->status_produk == 'NPL' ? 'selected' : '' }}>NPL</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <!-- TABEL DINAMIS ITEM -->
        <div class="table-responsive mb-3 border rounded">
            <table class="table table-bordered table-sm align-middle m-0" id="excelTable">
                <thead class="table-light text-center small align-middle">
                    <tr>
                        <th style="min-width: 160px;">Kode / SKU Produk</th>
                        <th style="min-width: 65px;">ED</th>
                        <th style="min-width: 65px;">HM</th>
                        <th style="min-width: 65px;">KR</th>
                        <th style="min-width: 65px;">NS</th>
                        <th style="min-width: 65px;">NED</th>
                        <th style="min-width: 70px;">Isi/UoM</th>
                        <th style="min-width: 75px;">Tot PCS</th>
                        <th style="min-width: 75px;">Tot UoM</th>
                        <th style="min-width: 140px;">Total Harga (Rp)</th>
                        <th style="min-width: 220px;">Keterangan</th>
                        <th style="width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @foreach($items as $item)
                    @php
                        $prod = $produks->where('sku', $item->kode_produk)->first();
                        $uomDefault = $prod->uom ?? $prod->isi_per_karton ?? $prod->konversi ?? 1;
                        if ($uomDefault <= 0) $uomDefault = 1;
                        
                        // Kalkulasi manual uom jika datanya tersimpan desimal sebelumnya
                        $totalUomBulat = round($item->total_pcs / $uomDefault);
                    @endphp
                    <tr>
                        <td><input type="text" list="listProduk" name="kode_produk[]" class="form-control form-control-sm sku-input" value="{{ $item->kode_produk }}" required autocomplete="off"></td>
                        <td><input type="number" name="qty_ed[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ed }}"></td>
                        <td><input type="number" name="qty_hm[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_hm }}"></td>
                        <td><input type="number" name="qty_kr[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_kr }}"></td>
                        <td><input type="number" name="qty_ns[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ns }}"></td>
                        <td><input type="number" name="qty_ned[]" class="form-control form-control-sm qty-input text-center" min="0" value="{{ $item->qty_ned }}"></td>
                        <td><input type="number" name="input_uom[]" class="form-control form-control-sm uom-input text-center bg-white fw-bold text-success" min="1" value="{{ $uomDefault }}"></td>
                        <td><input type="text" class="form-control form-control-sm total-pcs text-center fw-bold text-primary bg-light" readonly value="{{ $item->total_pcs }}"></td>
                        <td><input type="text" class="form-control form-control-sm total-uom text-center fw-bold text-success bg-light" readonly value="{{ $totalUomBulat }}"></td>
                        <td>
                            <input type="text" class="form-control form-control-sm total-harga-display text-end fw-bold text-danger bg-light" readonly value="Rp {{ number_format($item->jumlah_harga, 0, ',', '.') }}">
                            <input type="hidden" name="jumlah_harga[]" class="total-harga-val" value="{{ $item->jumlah_harga }}">
                        </td>
                        <td><input type="text" name="keterangan_proling[]" class="form-control form-control-sm shortcut-keterangan" value="{{ $item->keterangan_proling }}"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger remove-row" tabindex="-1"><i class="bi bi-trash"></i></button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- TOMBOL BAWAH -->
        <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top align-items-center">
            <button type="button" class="btn btn-sm btn-success px-3 py-2" id="addRowBtn">
                <i class="bi bi-plus-circle me-1"></i> Tambah Baris SKU (Shortcut: Alt + N)
            </button>
            <a href="{{ route('form.bs.index') }}" class="btn btn-sm btn-secondary px-4 py-2">Batal</a>
            <button type="submit" class="btn btn-sm btn-primary px-5 py-2 fw-bold shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Perubahan
            </button>
        </div>

        <datalist id="listProduk">
            @foreach($produks as $p)
                @php
                    $hargaVal = $p->harga ?? $p->price ?? $p->hargajual ?? 0;
                    $konversiVal = $p->uom ?? $p->isi_per_karton ?? $p->konversi ?? 1;
                @endphp
                <option value="{{ $p->sku }}" data-harga="{{ $hargaVal }}" data-konversi="{{ $konversiVal }}">
                    {{ $p->keterangan ?? $p->nama_produk ?? '' }} (UoM: {{ $konversiVal }})
                </option>
            @endforeach
        </datalist>
    </form>
</div>

<!-- Script Kalkulasi & Shortcut -->
<script>
    const produkMap = {
        @foreach($produks as $p)
            @php
                $hargaVal = $p->harga ?? $p->price ?? $p->hargajual ?? 0;
                $konversiVal = $p->uom ?? $p->isi_per_karton ?? $p->konversi ?? 1;
            @endphp
            "{{ strtoupper($p->sku) }}": { harga: {{ $hargaVal }}, konversi: {{ $konversiVal > 0 ? $konversiVal : 1 }} },
        @endforeach
    };

    function formatRupiah(angka) { 
        return 'Rp ' + Number(angka).toLocaleString('id-ID'); 
    }

    function calculateRow(tr) {
        let skuInput = tr.querySelector('.sku-input');
        let sku = skuInput.value.trim().toUpperCase();
        let qtyInputs = tr.querySelectorAll('.qty-input');
        let uomInput = tr.querySelector('.uom-input');
        
        let totalPcs = 0;
        qtyInputs.forEach(input => { 
            totalPcs += (parseInt(input.value) || 0); 
        });

        let hargaSatuan = 0;

        if (produkMap[sku]) {
            hargaSatuan = produkMap[sku].harga;
            uomInput.value = produkMap[sku].konversi;
        }

        let pembagiUom = parseFloat(uomInput.value) || 1;
        if (pembagiUom <= 0) pembagiUom = 1;
        
        // Membulatkan total UoM menjadi tanpa desimal
        let totalUom = Math.round(totalPcs / pembagiUom);
        let totalHarga = totalPcs * hargaSatuan;

        tr.querySelector('.total-pcs').value = totalPcs;
        tr.querySelector('.total-uom').value = totalUom;
        tr.querySelector('.total-harga-val').value = totalHarga;
        tr.querySelector('.total-harga-display').value = formatRupiah(totalHarga);
    }

    function addNewRow() {
        let tbody = document.getElementById('tableBody');
        let firstRow = tbody.querySelector('tr');
        let newRow = firstRow.cloneNode(true); 

        newRow.querySelector('.sku-input').value = '';
        newRow.querySelectorAll('.qty-input').forEach(inp => inp.value = 0);
        newRow.querySelector('.uom-input').value = 1;
        newRow.querySelector('.total-pcs').value = 0;
        newRow.querySelector('.total-uom').value = 0;
        newRow.querySelector('.total-harga-display').value = 'Rp 0';
        newRow.querySelector('.total-harga-val').value = 0;
        
        let textInputs = newRow.querySelectorAll('input[type="text"]');
        textInputs.forEach(inp => inp.value = '');

        tbody.appendChild(newRow);
        newRow.querySelector('.sku-input').focus();
    }

    document.getElementById('tableBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('sku-input') || e.target.classList.contains('uom-input')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    document.getElementById('tableBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('sku-input') || e.target.classList.contains('uom-input')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    document.getElementById('tableBody').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.classList.contains('shortcut-keterangan')) {
            e.preventDefault();
            addNewRow();
        }
    });

    document.getElementById('addRowBtn').addEventListener('click', function() {
        addNewRow();
    });

    document.addEventListener('keydown', function(e) {
        if (e.altKey && e.key.toLowerCase() === 'n') {
            e.preventDefault();
            addNewRow();
        }
    });

    document.getElementById('tableBody').addEventListener('click', function(e) {
        let deleteBtn = e.target.closest('.remove-row');
        if (deleteBtn) {
            let tbody = document.getElementById('tableBody');
            if (tbody.querySelectorAll('tr').length > 1) {
                deleteBtn.closest('tr').remove();
            } else {
                alert('Tidak bisa menghapus! Minimal harus ada 1 baris.');
            }
        }
    });
</script>
@endsection