@extends('layouts.admin')

@section('title', 'Edit Retur BS')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-primary me-2"></i>Edit Transaksi Retur BS: {{ $retur->nama_toko }}
        </h5>
        <a href="{{ route('retur.history.detail', $retur->tanggal_bon_in) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <hr class="mb-4 text-muted">

    <form action="{{ route('retur.update', $retur->id) }}" method="POST" id="returForm">
        @csrf
        @method('PUT')
        
        <!-- Header Informasi Bon & Toko -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon In</label>
                <input type="date" name="tanggal_bon_in" class="form-control" value="{{ $retur->tanggal_bon_in }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Nama Toko</label>
                <input type="text" name="nama_toko" class="form-control" value="{{ $retur->nama_toko }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Driver</label>
                <input type="text" name="driver" class="form-control" value="{{ $retur->driver }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">B2B</label>
                <input type="text" name="b2b" class="form-control" value="{{ $retur->b2b }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">NRB Outlet</label>
                <input type="text" name="nrb_outlet" class="form-control" value="{{ $retur->nrb_outlet }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon</label>
                <input type="date" name="tanggal_bon" class="form-control" value="{{ $retur->tanggal_bon }}">
            </div>
        </div>

        <hr class="my-4 text-muted">

        <!-- Tabel Item Produk Retur -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0">Daftar Produk Retur</h6>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary fw-semibold" id="addRow">
                <i class="bi bi-plus-lg me-1"></i> Tambah Baris Produk
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="20%">Pilih SKU</th>
                        <th width="9%">Qty</th>
                        <th width="11%">Status</th>
                        <th width="17%">Barcode</th>
                        <th width="16%">Total Harga (Rp)</th>
                        <th width="22%">Deskripsi Produk</th>
                        <th width="5%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($retur->items as $index => $item)
                    <tr class="product-row">
                        <td>
                            <input type="text" list="listProduk" name="items[{{ $index }}][sku]" class="form-control sku-input" value="{{ $item->sku }}" required autocomplete="off">
                            <div class="invalid-feedback sku-error d-none text-danger small mt-1">SKU tidak ditemukan!</div>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" min="1" required>
                        </td>
                        <td>
                            <input type="hidden" name="items[{{ $index }}][status]" class="status-hidden-input" value="{{ $item->status }}">
                            <div class="status-badge-container">
                                @php
                                    $statusUpper = strtoupper($item->status ?? '');
                                    $badgeClass = 'bg-secondary text-white';
                                    if (str_contains($statusUpper, 'EXISTING')) $badgeClass = 'bg-success text-white';
                                    elseif (str_contains($statusUpper, 'NPL')) $badgeClass = 'bg-danger text-white';
                                    elseif (str_contains($statusUpper, 'PROCHIZ')) $badgeClass = 'bg-warning text-dark';
                                    elseif (str_contains($statusUpper, 'QUAKER OAT')) $badgeClass = 'bg-info text-dark';
                                    elseif (str_contains($statusUpper, 'SKIPPY')) $badgeClass = 'bg-primary text-white';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2 py-1 fw-bold">{{ $item->status ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][barcode]" class="form-control barcode-input bg-light font-monospace" value="{{ $item->barcode }}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control price-display" value="Rp {{ number_format($item->sum_price, 0, ',', '.') }}" readonly>
                            <input type="hidden" name="items[{{ $index }}][sum_price]" class="price-hidden-input" value="{{ $item->sum_price }}" required>
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][keterangan]" class="form-control keterangan-input" value="{{ $item->keterangan }}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <datalist id="listProduk">
            @foreach($produks as $p)
                <option value="{{ $p->sku }}" data-harga="{{ $p->harga }}" data-status="{{ $p->status }}" data-barcode="{{ $p->barcode }}" data-keterangan="{{ $p->keterangan }}">
                    {{ $p->sku }} - {{ $p->keterangan }} (Rp {{ number_format($p->harga, 0, ',', '.') }})
                </option>
            @endforeach
        </datalist>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('retur.history.detail', $retur->tanggal_bon_in) }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">Simpan Perubahan (Ctrl+Enter)</button>
        </div>
    </form>
</div>

<script>
    let rowIndex = {{ $retur->items->count() }};
    const produkData = {
        @foreach($produks as $p)
            "{{ $p->sku }}": {
                harga: {{ $p->harga ?? 0 }},
                status: "{{ $p->status }}",
                barcode: "{{ $p->barcode ?? '' }}",
                keterangan: "{{ $p->keterangan ?? '' }}"
            },
        @endforeach
    };

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    function getBadgeClass(status) {
        if (!status) return 'bg-secondary text-white';
        let upper = status.toUpperCase();
        if (upper.includes('EXISTING')) return 'bg-success text-white';
        if (upper.includes('NPL')) return 'bg-danger text-white';
        if (upper.includes('PROCHIZ')) return 'bg-warning text-dark';
        if (upper.includes('SEASONAL') || upper.includes('SUSU') || upper.includes('JMK')) return 'bg-secondary text-white';
        if (upper.includes('QUAKER OAT')) return 'bg-info text-dark';
        if (upper.includes('SKIPPY')) return 'bg-primary text-white';
        return 'bg-secondary text-white';
    }

    function addRow() {
        let tbody = document.querySelector('#itemsTable tbody');
        let newRow = document.createElement('tr');
        newRow.classList.add('product-row');

        newRow.innerHTML = `
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
                <input type="text" class="form-control price-display" placeholder="Rp 0" readonly>
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
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    // Event listener terpadu untuk SKU dan Qty secara real-time
    document.addEventListener('input', function(e) {
        if (e.target && (e.target.classList.contains('sku-input') || e.target.classList.contains('qty-input'))) {
            let row = e.target.closest('tr');
            let skuInput = row.querySelector('.sku-input');
            let skuVal = skuInput.value.trim();
            let errorDiv = row.querySelector('.sku-error');

            if (produkData[skuVal]) {
                let data = produkData[skuVal];
                errorDiv.classList.add('d-none');
                skuInput.classList.remove('is-invalid');

                row.querySelector('.status-hidden-input').value = data.status;
                let badgeClass = getBadgeClass(data.status);
                row.querySelector('.status-badge-container').innerHTML = `<span class="badge ${badgeClass} px-2 py-1 fw-bold">${data.status}</span>`;

                row.querySelector('.barcode-input').value = data.barcode;
                
                // Isi keterangan jika baru pertama kali / SKU berubah
                if (e.target.classList.contains('sku-input') && !row.querySelector('.keterangan-input').value) {
                    row.querySelector('.keterangan-input').value = data.keterangan;
                }

                let qty = parseFloat(row.querySelector('.qty-input').value) || 1;
                let totalHarga = data.harga * qty;
                row.querySelector('.price-hidden-input').value = totalHarga;
                row.querySelector('.price-display').value = formatRupiah(totalHarga);
            } else if (e.target.classList.contains('sku-input') && skuVal !== '') {
                errorDiv.classList.remove('d-none');
                skuInput.classList.add('is-invalid');
                
                row.querySelector('.status-hidden-input').value = '';
                row.querySelector('.status-badge-container').innerHTML = `<span class="badge bg-secondary text-white">-</span>`;
                row.querySelector('.barcode-input').value = '';
                row.querySelector('.price-hidden-input').value = '';
                row.querySelector('.price-display').value = '';
            }
        }
    });

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

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row')) {
            let tbody = document.querySelector('#itemsTable tbody');
            if (tbody.rows.length > 1) {
                e.target.closest('tr').remove();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('returForm').submit();
        }
    });
</script>
@endsection