@extends('layouts.admin')

@section('title', 'Input Data Karung')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-box-seam text-primary me-2"></i>Form Input Karung
        </h5>
        <a href="{{ route('karung.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <hr class="mb-4 text-muted">

    <!-- Menampilkan Error Tanpa Foreach -->
    @if ($errors->any())
        <div class="alert alert-danger rounded-3 py-2 small mb-4">
            <ul class="mb-0 ps-3">
                {!! implode('', $errors->all('<li>:message</li>')) !!}
            </ul>
        </div>
    @endif

    <form action="{{ route('karung.store') }}" method="POST" id="karungForm">
        @csrf
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" id="inputTanggal" class="form-control trigger-generate" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Produk <span class="text-danger">*</span></label>
                <select name="jenis_produk" id="inputJenis" class="form-select trigger-generate" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="ATOM">ATOM</option>
                    <option value="BISKUIT">BISKUIT</option>
                    <option value="CHOCOLATOS">CHOCOLATOS</option>
                    <option value="KACANG">KACANG</option>
                    <option value="KEJU">KEJU</option>
                    <option value="SKIPPY">SKIPPY</option>
                    <option value="OCORN">O'CORN</option>
                    <option value="SEASONAL">SEASONAL</option>
                    <option value="JELY">JELY</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" id="inputStatus" class="form-select trigger-generate" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Reguler">Reguler</option>
                    <option value="Prochiz">Prochiz</option>
                    <option value="Skippy">Skippy</option>
                    <option value="NPL">NPL</option>
                    <option value="Seasonal">Seasonal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">No Karung (Manual/Preview)</label>
                <div class="input-group">
                    <input type="text" name="no_karung" id="inputNoKarung" class="form-control" placeholder="KRG-..." required>
                    <button type="button" class="btn btn-outline-primary" id="btnGenerate" title="Generate Ulang">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>
                <small class="text-muted" style="font-size: 0.75rem;">Bisa diubah manual</small>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0">Detail Item (SKU & Qty) <small class="text-muted fw-normal font-monospace"></h6>
            </div>
            <button type="button" class="btn btn-success btn-sm px-3 rounded-pill" id="addRow">
                <i class="bi bi-plus-lg me-1"></i> Tambah Baris (Alt + N)
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="35%">Pilih SKU</th>
                        <th width="15%">Qty</th>
                        <th width="40%">Keterangan Produk</th>
                        <th width="5%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="product-row">
                        <td class="row-number text-center fw-bold align-middle">1</td>
                        <td>
                            <input type="text" list="listProduk" name="items[0][sku]" class="form-control sku-input" placeholder="Ketik SKU..." required autocomplete="off">
                            <div class="invalid-feedback sku-error d-none text-danger small mt-1">SKU tidak ditemukan!</div>
                        </td>
                        <td>
                            <input type="number" name="items[0][qty]" class="form-control qty-input" value="1" min="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control keterangan-input bg-light" placeholder="Keterangan otomatis..." readonly>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Datalist dikosongkan agar aman dari error compiler Blade -->
        <datalist id="listProduk"></datalist>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('karung.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold" id="submitBtn">Simpan Data Karung (Ctrl+Enter)</button>
        </div>
    </form>
</div>

@php
    $produkJson = [];
    if(isset($produks)) {$produkJson = $produks->keyBy('sku')->map(function($p) {
            return [
                'keterangan' => $p->keterangan ?? $p->nama_produk ?? ''
            ];
        });
    }
@endphp

<script>
    let rowIndex = 1;
    let manualOverride = false; 
    
    // Ambil data JSON dengan bersih
    const produkData = @json($produkJson);

    // Populate Datalist Produk via JavaScript
    const listProdukEl = document.getElementById('listProduk');
    if (listProdukEl && typeof produkData === 'object') {
        for (let sku in produkData) {
            let p = produkData[sku];
            let opt = document.createElement('option');
            opt.value = sku;
            opt.textContent = `${sku} - ${p.keterangan}`;
            listProdukEl.appendChild(opt);
        }
    }

    // Fungsi mengambil 3 huruf terakhir dari SKU baris pertama
    function getSkuSuffix() {
        let firstSkuInput = document.querySelector('.sku-input'); // Ambil baris pertama
        if (firstSkuInput && firstSkuInput.value.length >= 3) {
            let val = firstSkuInput.value.trim();
            return val.substring(val.length - 3).toUpperCase();
        }
        return 'XXX'; // Placeholder jika SKU belum diketik / kurang dari 3 huruf
    }

    // Fungsi Auto-Generate No Karung sesuai SOP
    function generateNoKarung() {
        if(manualOverride) return; // Hentikan jika user sudah mengetik manual

        let tgl = document.getElementById('inputTanggal').value;
        let jenis = document.getElementById('inputJenis').value;
        let status = document.getElementById('inputStatus').value;

        if (jenis && status && tgl) {
            // Kalkulasi Week (Minggu ke berapa dalam bulan tersebut)
            let dateObj = new Date(tgl);
            let monthNames = ["JAN", "FEB", "MAR", "APR", "MEI", "JUN", "JUL", "AGU", "SEP", "OKT", "NOV", "DES"];
            let monthStr = monthNames[dateObj.getMonth()];
            let day = dateObj.getDate();
            let week = Math.ceil(day / 7);
            let weekStr = monthStr + week; // Hasil: SEP3, AGU2, dsb

            let prefix = '';
            let suffix = weekStr;

            // 1. Tentukan Prefix & Suffix berdasarkan Aturan
            if (status === 'NPL') {
                prefix = 'NPL';
                suffix = getSkuSuffix();
            } else if (status === 'Skippy' || jenis === 'SKIPPY') {
                prefix = 'SK';
                suffix = getSkuSuffix();
            } else if (status === 'Seasonal' || jenis === 'SEASONAL') {
                prefix = 'SES';
            } else if (jenis === 'ATOM' || jenis === 'OCORN') {
                prefix = 'A';
            } else if (jenis === 'BISKUIT' || jenis === 'CHOCOLATOS') {
                prefix = 'B';
            } else if (jenis === 'KACANG') {
                prefix = 'K';
            } else if (jenis === 'KEJU') {
                prefix = 'PR';
            } else {
                prefix = jenis.substring(0, 3).toUpperCase(); // Default fallback
            }

            // 2. Ambil No Urut Karung dari Controller (Format 2 digit: 01, 02, dst)
            let noUrut = "{{ str_pad($nextUrut ?? 1, 2, '0', STR_PAD_LEFT) }}";

            // 3. Gabungkan Format: Prefix.NoUrut.Suffix
            document.getElementById('inputNoKarung').value = `${prefix}.${noUrut}.${suffix}`;
        }
    }

    // Trigger generate saat dropdown/tanggal berubah
    document.querySelectorAll('.trigger-generate').forEach(el => {
        el.addEventListener('change', generateNoKarung);
    });

    // Deteksi manual typing
    document.getElementById('inputNoKarung').addEventListener('input', function() {
        manualOverride = true;
    });

    // Tombol force generate ulang
    document.getElementById('btnGenerate').addEventListener('click', function() {
        manualOverride = false;
        generateNoKarung();
    });

    // ==========================================
    // LOGIKA PENGECEKAN DOUBLE SKU
    // ==========================================
    function checkDuplicateSkus() {
        let skuInputs = document.querySelectorAll('.sku-input');
        let seenSkus = {};

        // 1. Reset state (hapus peringatan)
        skuInputs.forEach(input => {
            let val = input.value.trim();
            let row = input.closest('tr');
            let errorDiv = row.querySelector('.sku-error');
            
            // Jika SKU ada di master data, asumsikan aman dulu
            if (val && produkData[val]) {
                input.classList.remove('is-invalid');
                errorDiv.classList.add('d-none');
            }
        });

        // 2. Tandai yang double
        skuInputs.forEach(input => {
            let sku = input.value.trim();
            if (sku && produkData[sku]) { 
                if (seenSkus[sku]) {
                    seenSkus[sku].push(input.closest('tr'));
                    // Tandai baris sebelumnya & baris saat ini sebagai double
                    seenSkus[sku].forEach(row => {
                        let inp = row.querySelector('.sku-input');
                        let err = row.querySelector('.sku-error');
                        inp.classList.add('is-invalid');
                        err.textContent = 'SKU sudah diinput di baris lain!';
                        err.classList.remove('d-none');
                    });
                } else {
                    seenSkus[sku] = [input.closest('tr')];
                }
            }
        });
    }

    // Fungsi Tabel SKU
    function updateRowNumbers() {
        document.querySelectorAll('.product-row').forEach((row, index) => {
            let numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = index + 1;
        });
    }

    function addRow() {
        let tbody = document.querySelector('#itemsTable tbody');
        let newRow = document.createElement('tr');
        newRow.classList.add('product-row');

        newRow.innerHTML = `
            <td class="row-number text-center fw-bold align-middle"></td>
            <td>
                <input type="text" list="listProduk" name="items[${rowIndex}][sku]" class="form-control sku-input" placeholder="Ketik SKU..." required autocomplete="off">
                <div class="invalid-feedback sku-error d-none text-danger small mt-1">SKU tidak ditemukan!</div>
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][qty]" class="form-control qty-input" value="1" min="1" required>
            </td>
            <td>
                <input type="text" class="form-control keterangan-input bg-light" placeholder="Keterangan otomatis..." readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(newRow);
        rowIndex++;
        updateRowNumbers();
        
        // Auto-fokus ke kolom SKU baris baru
        newRow.querySelector('.sku-input').focus();
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    // Auto-fill Keterangan Produk & Deteksi Double SKU
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('sku-input')) {
            let row = e.target.closest('tr');
            let skuVal = e.target.value.trim();
            let errorDiv = row.querySelector('.sku-error');
            let ketInput = row.querySelector('.keterangan-input');

            // Cek Master Data Terlebih Dahulu
            if (produkData[skuVal]) {
                errorDiv.classList.add('d-none');
                e.target.classList.remove('is-invalid');
                ketInput.value = produkData[skuVal].keterangan;
            } else if (skuVal !== '') {
                errorDiv.textContent = 'SKU tidak ditemukan!';
                errorDiv.classList.remove('d-none');
                e.target.classList.add('is-invalid');
                ketInput.value = '';
            } else {
                errorDiv.classList.add('d-none');
                e.target.classList.remove('is-invalid');
                ketInput.value = '';
            }

            // Jalankan deteksi double SKU setelah master data dicek
            checkDuplicateSkus();

            // Trigger Update No Karung secara Real-time karena akhiran (suffix) 
            // bergantung pada 3 huruf terakhir SKU (khusus NPL dan Skippy)
            generateNoKarung();
        }
    });

    // Tambah baris otomatis jika qty di baris terakhir diubah
    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('qty-input')) {
            let rows = document.querySelectorAll('.product-row');
            let currentRow = e.target.closest('tr');
            if (currentRow === rows[rows.length - 1]) {
                let skuVal = currentRow.querySelector('.sku-input').value.trim();
                if (produkData[skuVal]) {
                    addRow();
                }
            }
        }
    });

    // Hapus Baris & Re-evaluasi Double SKU
    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row')) {
            let tbody = document.querySelector('#itemsTable tbody');
            if (tbody.rows.length > 1) {
                e.target.closest('tr').remove();
                updateRowNumbers();
                checkDuplicateSkus(); // Cek lagi double SKU barangkali sudah tidak ganda
                generateNoKarung();   // Cek lagi nomor karung barangkali suffix berubah
            }
        }
    });

    // Panggil satu kali saat inisiasi halaman
    generateNoKarung();
    
    // Shortcut Ctrl+Enter untuk submit form, Alt+N / Ctrl+Shift+N untuk tambah baris
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('karungForm').submit();
        }

        if ((e.altKey && e.key.toLowerCase() === 'n') || (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'n')) {
            e.preventDefault();
            addRow();
        }
    });
</script>
@endsection