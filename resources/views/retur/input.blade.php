@extends('layouts.admin')

@section('title', 'Input Retur BS')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-arrow-return-left text-primary me-2"></i>Form Transaksi Retur BS
        </h5>
        <a href="{{ route('retur.dashboard') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <hr class="mb-4 text-muted">

    <form action="{{ route('retur.store') }}" method="POST" id="returForm">
        @csrf
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon In</label>
                <input type="date" name="tanggal_bon_in" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Nama Toko</label>
                <input type="text" name="nama_toko" class="form-control" placeholder="Nama Toko / Outlet..." required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Driver</label>
                <input type="text" name="driver" class="form-control" placeholder="Nama Driver..." required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">B2B (Cek / Cari NRB)</label>
                <input type="text" list="listB2b" name="b2b" id="b2bInput" class="form-control" placeholder="Ketik 4 digit akhir B2B..." autocomplete="off">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">NRB Outlet</label>
                <input type="text" name="nrb_outlet" class="form-control" placeholder="No NRB...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon</label>
                <input type="date" name="tanggal_bon" class="form-control">
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0">Daftar Produk Retur <small class="text-muted fw-normal font-monospace"></h6>
            </div>
            <button type="button" class="btn btn-success btn-sm px-3 rounded-pill" id="addRow">
                <i class="bi bi-plus-lg me-1"></i>Tambah Baris (Alt + N)
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="4%" class="text-center">No</th>
                        <th width="16%">Pilih SKU</th>
                        <th width="8%">Qty</th>
                        <th width="10%">Status</th>
                        <th width="15%">Barcode</th>
                        <th width="16%">Total Harga (Rp)</th>
                        <th width="26%">Deskripsi Produk</th>
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
                            <input type="hidden" name="items[0][status]" class="status-hidden-input">
                            <div class="status-badge-container">
                                <span class="badge bg-secondary text-white">-</span>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="items[0][barcode]" class="form-control barcode-input bg-light font-monospace" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control price-display text-success fw-bold" placeholder="Rp 0" readonly>
                            <input type="hidden" name="items[0][sum_price]" class="price-hidden-input" required>
                        </td>
                        <td>
                            <input type="text" name="items[0][keterangan]" class="form-control keterangan-input" placeholder="Deskripsi produk...">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="table-light fw-bold">
                        <td colspan="5" class="text-end">Grand Total:</td>
                        <td colspan="3"><span id="grandTotalDisplay" class="text-success fs-6">Rp 0</span></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <datalist id="listProduk"></datalist>
        <datalist id="listB2b"></datalist>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('retur.dashboard') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary fw-semibold px-4" id="submitBtn">Simpan (Ctrl+Enter)</button>
        </div>
    </form>
</div>

<script>
    let rowIndex = 1;
    
    // Data JSON diterima bersih dari controller
    const produkData = @json($produks);
    const b2bData = @json($b2bs);

    // Populate Datalist Produk via JavaScript
    const listProdukEl = document.getElementById('listProduk');
    if (listProdukEl) {
        for (let sku in produkData) {
            let p = produkData[sku];
            let opt = document.createElement('option');
            opt.value = sku;
            opt.textContent = `${sku} - ${p.keterangan} (Rp ${Number(p.harga).toLocaleString('id-ID')})`;
            listProdukEl.appendChild(opt);
        }
    }

    // Populate Datalist B2B via JavaScript
    const listB2bEl = document.getElementById('listB2b');
    if (listB2bEl) {
        for (let nrb in b2bData) {
            let b = b2bData[nrb];
            let opt = document.createElement('option');
            opt.value = b.nrb;
            opt.textContent = `${b.nrb} - ${b.nama_toko}`;
            listB2bEl.appendChild(opt);
        }
    }

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    function updateRowNumbers() {
        document.querySelectorAll('.product-row').forEach((row, index) => {
            let numCell = row.querySelector('.row-number');
            if (numCell) numCell.innerText = index + 1;
        });
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.price-hidden-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        
        let grandTotalEl = document.getElementById('grandTotalDisplay');
        grandTotalEl.innerText = formatRupiah(total);
        
        grandTotalEl.classList.remove('text-success', 'text-danger');
        if (total >= 1000000) {
            grandTotalEl.classList.add('text-danger');
        } else {
            grandTotalEl.classList.add('text-success');
        }
    }

    function getBadgeClass(status) {
        if (!status) return 'bg-secondary text-white';
        let upper = status.toUpperCase();
        if (upper.includes('EXISTING')) return 'bg-success text-white';
        if (upper.includes('NPL')) return 'bg-danger text-white';
        if (upper.includes('PROCHIZ')) return 'bg-warning text-dark';
        return 'bg-secondary text-white';
    }

    function checkDuplicateSku(currentInput) {
        let currentVal = currentInput.value.trim();
        if (!currentVal) return false;
        let rows = document.querySelectorAll('.product-row');
        let isDuplicate = false;
        rows.forEach(row => {
            let input = row.querySelector('.sku-input');
            if (input && input !== currentInput && input.value.trim() === currentVal) {
                isDuplicate = true;
            }
        });
        return isDuplicate;
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
                <input type="hidden" name="items[${rowIndex}][status]" class="status-hidden-input">
                <div class="status-badge-container">
                    <span class="badge bg-secondary text-white">-</span>
                </div>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][barcode]" class="form-control barcode-input bg-light font-monospace" readonly>
            </td>
            <td>
                <input type="text" class="form-control price-display fw-bold text-success" placeholder="Rp 0" readonly>
                <input type="hidden" name="items[${rowIndex}][sum_price]" class="price-hidden-input" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][keterangan]" class="form-control keterangan-input" placeholder="Deskripsi produk...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(newRow);
        rowIndex++;
        updateRowNumbers();
        calculateGrandTotal();
        
        newRow.querySelector('.sku-input').focus();
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    document.addEventListener('input', function(e) {
        if (e.target && (e.target.id === 'b2bInput' || e.target.name === 'b2b')) {
            let val = e.target.value.trim();
            let matchedB2b = null;

            for (let key in b2bData) {
                if (key === val || (val.length >= 4 && key.endsWith(val))) {
                    matchedB2b = b2bData[key];
                    break;
                }
            }

            if (matchedB2b) {
                let nrbInput = document.querySelector('input[name="nrb_outlet"]');
                if (nrbInput) {
                    nrbInput.value = matchedB2b.nrb ?? '';
                }

                let tokoInput = document.querySelector('input[name="nama_toko"]');
                if (tokoInput) {
                    tokoInput.value = matchedB2b.nama_toko;
                }

                let tbody = document.querySelector('#itemsTable tbody');
                if (tbody) {
                    tbody.innerHTML = '';
                    rowIndex = 0;

                    if (matchedB2b.items && Array.isArray(matchedB2b.items)) {
                        matchedB2b.items.forEach((item) => {
                            addRow();
                            let currentRow = tbody.rows[tbody.rows.length - 1];
                            let skuInput = currentRow.querySelector('.sku-input');
                            let qtyInput = currentRow.querySelector('.qty-input');
                            let ketInput = currentRow.querySelector('.keterangan-input');

                            if (skuInput) skuInput.value = item.sku;
                            if (qtyInput) qtyInput.value = item.qty;
                            if (ketInput) ketInput.value = item.keterangan ?? '';

                            skuInput.dispatchEvent(new Event('input', { bubbles: true }));
                        });
                    }
                }
            }
        }

        if (e.target && (e.target.classList.contains('sku-input') || e.target.classList.contains('qty-input'))) {
            let row = e.target.closest('tr');
            if (!row) return;
            let skuInput = row.querySelector('.sku-input');
            let skuVal = skuInput.value.trim();
            let errorDiv = row.querySelector('.sku-error');
            let priceDisplay = row.querySelector('.price-display');

            if (checkDuplicateSku(skuInput)) {
                errorDiv.textContent = 'SKU sudah diinput di baris lain!';
                errorDiv.classList.remove('d-none');
                skuInput.classList.add('is-invalid');
                
                row.querySelector('.status-hidden-input').value = '';
                row.querySelector('.status-badge-container').innerHTML = `<span class="badge bg-secondary text-white">-</span>`;
                row.querySelector('.barcode-input').value = '';
                row.querySelector('.price-hidden-input').value = '';
                if (priceDisplay) priceDisplay.value = '';
                calculateGrandTotal();
                return;
            }

            if (produkData[skuVal]) {
                let data = produkData[skuVal];
                if (errorDiv) errorDiv.classList.add('d-none');
                skuInput.classList.remove('is-invalid');

                let statusVal = data.status ?? '-';
                row.querySelector('.status-hidden-input').value = statusVal;
                let badgeClass = getBadgeClass(statusVal);
                row.querySelector('.status-badge-container').innerHTML = `<span class="badge ${badgeClass} px-2 py-1 fw-bold">${statusVal}</span>`;

                row.querySelector('.barcode-input').value = data.barcode ?? '';
                
                let ketInput = row.querySelector('.keterangan-input');
                if (e.target.classList.contains('sku-input') && ketInput && !ketInput.value) {
                    ketInput.value = data.keterangan ?? '';
                }

                let qty = parseFloat(row.querySelector('.qty-input').value) || 1;
                let hargaSatuan = parseFloat(data.harga ?? 0);
                let totalHarga = hargaSatuan * qty;
                row.querySelector('.price-hidden-input').value = totalHarga;
                
                if (priceDisplay) {
                    priceDisplay.value = formatRupiah(totalHarga);
                    priceDisplay.classList.remove('text-success', 'text-danger');
                    if (totalHarga >= 1000000) {
                        priceDisplay.classList.add('text-danger');
                    } else {
                        priceDisplay.classList.add('text-success');
                    }
                }
            } else {
                if (errorDiv) errorDiv.classList.add('d-none');
                skuInput.classList.remove('is-invalid');
                
                let statusHidden = row.querySelector('.status-hidden-input');
                if (statusHidden) statusHidden.value = '';
                
                let badgeContainer = row.querySelector('.status-badge-container');
                if (badgeContainer) badgeContainer.innerHTML = `<span class="badge bg-secondary text-white">-</span>`;
                
                let barcodeInput = row.querySelector('.barcode-input');
                if (barcodeInput) barcodeInput.value = '';
                
                let priceHidden = row.querySelector('.price-hidden-input');
                if (priceHidden) priceHidden.value = '';
                
                if (priceDisplay) {
                    priceDisplay.value = '';
                    priceDisplay.classList.remove('text-danger', 'text-success');
                }
            }
            calculateGrandTotal();
        }
    });

    document.addEventListener('blur', function(e) {
        if (e.target && e.target.classList.contains('sku-input')) {
            let row = e.target.closest('tr');
            if (!row) return;
            let skuVal = e.target.value.trim();
            let errorDiv = row.querySelector('.sku-error');

            if (checkDuplicateSku(e.target)) {
                errorDiv.textContent = 'SKU sudah diinput di baris lain!';
                errorDiv.classList.remove('d-none');
                e.target.classList.add('is-invalid');
            } else if (skuVal !== '' && !produkData[skuVal]) {
                errorDiv.textContent = 'SKU tidak ditemukan!';
                errorDiv.classList.remove('d-none');
                e.target.classList.add('is-invalid');
            } else {
                if (errorDiv) errorDiv.classList.add('d-none');
                e.target.classList.remove('is-invalid');
            }
        }
    }, true);

    document.addEventListener('change', function(e) {
        if (e.target && e.target.classList.contains('qty-input')) {
            let rows = document.querySelectorAll('.product-row');
            let currentRow = e.target.closest('tr');
            if (currentRow === rows[rows.length - 1]) {
                let skuInput = currentRow.querySelector('.sku-input');
                let skuVal = skuInput ? skuInput.value.trim() : '';
                if (produkData[skuVal]) {
                    addRow();
                }
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row')) {
            let tbody = document.querySelector('#itemsTable tbody');
            if (tbody.rows.length > 1) {
                e.target.closest('tr').remove();
                updateRowNumbers();
                calculateGrandTotal();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('returForm').submit();
        }

        if ((e.altKey && e.key.toLowerCase() === 'n') || (e.ctrlKey && e.shiftKey && e.key.toLowerCase() === 'n')) {
            e.preventDefault();
            addRow();
        }
    });

    updateRowNumbers();
</script>
@endsection