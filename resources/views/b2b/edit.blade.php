@extends('layouts.admin')

@section('title', 'Edit Transaksi B2B')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-primary me-2"></i>Form Edit Transaksi B2B
        </h5>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <hr class="mb-4 text-muted">

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 py-2 small mb-4">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('b2b.update', $b2b->id) }}" method="POST" id="b2bForm">
        @csrf
        @method('PUT')
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon In <span class="text-danger">*</span></label>
                <input type="date" name="tanggal_bon_in" class="form-control" value="{{ $b2b->tanggal_bon_in }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Nama Toko <span class="text-danger">*</span></label>
                <input type="text" name="nama_toko" class="form-control" value="{{ $b2b->nama_toko }}" placeholder="Nama Toko / Outlet..." required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">NRB Outlet</label>
                <input type="text" name="nrb_outlet" class="form-control" value="{{ $b2b->nrb_outlet }}" placeholder="No NRB...">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Outlet <span class="text-danger">*</span></label>
                <select name="outlet" class="form-select" required>
                    <option value="">-- Pilih Outlet --</option>
                    <option value="SAT" {{ $b2b->outlet == 'SAT' ? 'selected' : '' }}>SAT</option>
                    <option value="MIDI" {{ $b2b->outlet == 'MIDI' ? 'selected' : '' }}>MIDI</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal Bon</label>
                <input type="date" name="tanggal_bon" class="form-control" value="{{ $b2b->tanggal_bon }}">
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold text-dark m-0">Detail Item Produk B2B</h6>
            </div>
            <button type="button" class="btn btn-success btn-sm px-3 rounded-pill" id="addRowBtn">
                <i class="bi bi-plus-lg me-1"></i> Tambah Item
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th width="22%">Pilih SKU</th>
                        <th width="9%">Qty</th>
                        <th width="16%">Warning (Double SKU)</th>
                        <th width="18%">Sum Price (Rp)</th>
                        <th width="30%">Keterangan</th>
                        <th width="5%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($b2b->items as $index => $item)
                    <tr class="product-row">
                        <td>
                            <input type="text" list="listProduk" name="items[{{ $index }}][sku]" class="form-control sku-input" value="{{ $item->sku }}" placeholder="Ketik SKU..." required autocomplete="off">
                            <div class="invalid-feedback sku-error d-none text-danger small mt-1">SKU tidak ditemukan!</div>
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" min="1" required>
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][warning]" class="form-control warning-input text-success fw-bold text-center bg-light" value="{{ $item->warning ?? 'Aman' }}" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control price-display bg-light" value="Rp {{ number_format($item->sum_price, 0, ',', '.') }}" placeholder="Rp 0" readonly>
                            <input type="hidden" name="items[{{ $index }}][sum_price]" class="price-hidden-input" value="{{ $item->sum_price }}" required>
                        </td>
                        <td>
                            <input type="text" name="items[{{ $index }}][keterangan]" class="form-control keterangan-input" value="{{ $item->keterangan }}" placeholder="Keterangan produk...">
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
                <option value="{{ $p->sku }}" data-harga="{{ $p->harga ?? $p->price ?? 0 }}" data-keterangan="{{ $p->keterangan ?? $p->nama_produk ?? '' }}">
                    {{ $p->sku }} - {{ $p->keterangan ?? $p->nama_produk ?? '' }} (Rp {{ number_format($p->harga ?? $p->price ?? 0, 0, ',', '.') }})
                </option>
            @endforeach
        </datalist>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('b2b.sat') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-primary px-4 fw-semibold" id="submitBtn">Perbarui Transaksi B2B (Ctrl+Enter)</button>
        </div>
    </form>
</div>

<script>
    let rowIndex = {{ count($b2b->items) }};
    const produkData = {
        @foreach($produks as $p)
            "{{ $p->sku }}": {
                harga: {{ $p->harga ?? $p->price ?? 0 }},
                keterangan: "{{ $p->keterangan ?? $p->nama_produk ?? '' }}"
            },
        @endforeach
    };

    function formatRupiah(angka) {
        return 'Rp ' + Number(angka).toLocaleString('id-ID');
    }

    function checkDuplicateSkus() {
        let skuInputs = document.querySelectorAll('.sku-input');
        let seenSkus = {};

        skuInputs.forEach(input => {
            let row = input.closest('tr');
            row.querySelector('.warning-input').value = 'Aman';
            row.querySelector('.warning-input').classList.remove('text-danger');
            row.querySelector('.warning-input').classList.add('text-success');
        });

        skuInputs.forEach(input => {
            let sku = input.value.trim();
            if (sku) {
                if (seenSkus[sku]) {
                    seenSkus[sku].forEach(row => {
                        let warn = row.querySelector('.warning-input');
                        warn.value = '⚠️ Double SKU!';
                        warn.classList.remove('text-success');
                        warn.classList.add('text-danger');
                    });
                    let currentWarn = input.closest('tr').querySelector('.warning-input');
                    currentWarn.value = '⚠️ Double SKU!';
                    currentWarn.classList.remove('text-success');
                    currentWarn.classList.add('text-danger');
                } else {
                    seenSkus[sku] = [input.closest('tr')];
                }
            }
        });
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
                <input type="text" name="items[${rowIndex}][warning]" class="form-control warning-input text-success fw-bold text-center bg-light" value="Aman" readonly>
            </td>
            <td>
                <input type="text" class="form-control price-display bg-light" placeholder="Rp 0" readonly>
                <input type="hidden" name="items[${rowIndex}][sum_price]" class="price-hidden-input" required>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][keterangan]" class="form-control keterangan-input" placeholder="Keterangan produk...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(newRow);
        rowIndex++;
        checkDuplicateSkus();
    }

    document.getElementById('addRowBtn').addEventListener('click', addRow);

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
                row.querySelector('.price-hidden-input').value = '';
                row.querySelector('.price-display').value = '';
            }
            checkDuplicateSkus();
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
                checkDuplicateSkus();
            }
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('b2bForm').submit();
        }
    });
</script>
@endsection