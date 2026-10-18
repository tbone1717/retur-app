@extends('layouts.admin')

@section('title', 'Edit Data Karung')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-pencil-square text-warning me-2"></i>Form Edit Karung
        </h5>
        <a href="{{ route('karung.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
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

    <form action="{{ route('karung.update', $karung->id) }}" method="POST" id="karungForm">
        @csrf
        @method('PUT')
        
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $karung->tanggal }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Jenis Produk <span class="text-danger">*</span></label>
                <select name="jenis_produk" class="form-select" required>
                    <option value="ATOM" @selected($karung->jenis_produk == 'ATOM')>ATOM</option>
                    <option value="BISKUIT" @selected($karung->jenis_produk == 'BISKUIT')>BISKUIT</option>
                    <option value="CHOCOLATOS" @selected($karung->jenis_produk == 'CHOCOLATOS')>CHOCOLATOS</option>
                    <option value="KACANG" @selected($karung->jenis_produk == 'KACANG')>KACANG</option>
                    <option value="KEJU" @selected($karung->jenis_produk == 'KEJU')>KEJU</option>
                    <option value="SKIPPY" @selected($karung->jenis_produk == 'SKIPPY')>SKIPPY</option>
                    <option value="OCORN" @selected($karung->jenis_produk == 'OCORN')>O'CORN</option>
                    <option value="SEASONAL" @selected($karung->jenis_produk == 'SEASONAL')>SEASONAL</option>
                    <option value="JELY" @selected($karung->jenis_produk == 'JELY')>JELY</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="Reguler" @selected($karung->status == 'Reguler')>Reguler</option>
                    <option value="Prochiz" @selected($karung->status == 'Prochiz')>Prochiz</option>
                    <option value="Skippy" @selected($karung->status == 'Skippy')>Skippy</option>
                    <option value="NPL" @selected($karung->status == 'NPL')>NPL</option>
                    <option value="Seasonal" @selected($karung->status == 'Seasonal')>Seasonal</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">No Karung</label>
                <input type="text" name="no_karung" class="form-control" value="{{ $karung->no_karung }}" required>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold text-dark m-0">Detail Item (SKU & Qty)</h6>
            <button type="button" class="btn btn-success btn-sm px-3 rounded-pill" id="addRow">
                <i class="bi bi-plus-lg me-1"></i> Tambah Item
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
                    @foreach($karung->items as $index => $item)
                    <tr class="product-row">
                        <td class="row-number text-center fw-bold align-middle">{{ $index + 1 }}</td>
                        <td>
                            <input type="text" list="listProduk" name="items[{{ $index }}][sku]" class="form-control sku-input" value="{{ $item->sku }}" required autocomplete="off">
                        </td>
                        <td>
                            <input type="number" name="items[{{ $index }}][qty]" class="form-control qty-input" value="{{ $item->qty }}" min="1" required>
                        </td>
                        <td>
                            <input type="text" class="form-control keterangan-input bg-light" value="{{ $item->produk->keterangan ?? $item->produk->nama_produk ?? '' }}" readonly>
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
                <option value="{{ $p->sku }}" data-keterangan="{{ $p->keterangan ?? $p->nama_produk ?? '' }}">
                    {{ $p->sku }} - {{ $p->keterangan ?? $p->nama_produk ?? '' }}
                </option>
            @endforeach
        </datalist>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('karung.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-warning px-4 fw-semibold text-dark" id="submitBtn">Simpan Perubahan Karung (Ctrl+Enter)</button>
        </div>
    </form>
</div>

<script>
    let rowIndex = {{ $karung->items->count() }};
    const produkData = {
        @foreach($produks as $p)
            "{{ $p->sku }}": {
                keterangan: "{{ $p->keterangan ?? $p->nama_produk ?? '' }}"
            },
        @endforeach
    };

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
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('sku-input')) {
            let row = e.target.closest('tr');
            let skuVal = e.target.value.trim();
            let ketInput = row.querySelector('.keterangan-input');

            if (produkData[skuVal]) {
                ketInput.value = produkData[skuVal].keterangan;
            } else {
                ketInput.value = '';
            }
        }
    });

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest('.remove-row')) {
            let tbody = document.querySelector('#itemsTable tbody');
            if (tbody.rows.length > 1) {
                e.target.closest('tr').remove();
                updateRowNumbers();
            }
        }
    });

    // Shortcut Ctrl+Enter untuk submit form edit
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('karungForm').submit();
        }
    });
</script>
@endsection