@extends('layouts.admin')

@section('title', 'Input Pemusnahan Karung')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-trash3 text-danger me-2"></i>Form Pengajuan Pemusnahan Karung
        </h5>
        <a href="{{ route('pemusnahan.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
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

    <form action="{{ route('pemusnahan.store') }}" method="POST" id="pemusnahanForm">
        @csrf
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Pilih Tahap Pemusnahan <span class="text-danger">*</span></label>
                <select name="tahap" class="form-select" required>
                    <option value="">-- Pilih Tahap (1 - 4) --</option>
                    <option value="1">Tahap 1</option>
                    <option value="2">Tahap 2</option>
                    <option value="3">Tahap 3</option>
                    <option value="4">Tahap 4</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <h6 class="fw-bold text-dark m-0">Pilih Daftar Karung (Checklist)</h6>
            <div style="width: 250px;">
                <input type="text" id="searchKarung" class="form-control form-control-sm" placeholder="Cari No Karung / Jenis...">
            </div>
        </div>

        <div class="table-responsive mb-4 border rounded" style="max-height: 320px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle bg-white mb-0">
                <thead class="table-light sticky-top">
                    <tr>
                        <th width="5%" class="text-center">
                            <input type="checkbox" id="selectAll" class="form-check-input">
                        </th>
                        <th width="15%">Tanggal</th>
                        <th width="25%">No Karung</th>
                        <th width="20%">Jenis Produk</th>
                        <th width="15%">Status</th>
                        <th width="20%">Isi (SKU / Qty)</th>
                    </tr>
                </thead>
                <tbody id="karungTableBody">
                    @forelse($karungs as $k)
                    <tr class="karung-row">
                        <td class="text-center">
                            <input type="checkbox" name="karung_ids[]" value="{{ $k->id }}" class="form-check-input karung-checkbox" data-karung='@json($k)'>
                        </td>
                        <td>{{ date('d M Y', strtotime($k->tanggal)) }}</td>
                        <td class="fw-bold text-primary karung-no">{{ $k->no_karung }}</td>
                        <td class="karung-jenis">{{ $k->jenis_produk }}</td>
                        <td>
                            @php
                                $badgeColor = 'bg-secondary';
                                if($k->status == 'Reguler') $badgeColor = 'bg-success';
                                elseif($k->status == 'Prochiz') $badgeColor = 'bg-warning text-dark';
                                elseif($k->status == 'NPL') $badgeColor = 'bg-danger';
                                elseif($k->status == 'Skippy') $badgeColor = 'bg-info text-dark';
                                elseif($k->status == 'Seasonal') $badgeColor = 'bg-primary';
                            @endphp
                            <span class="badge {{ $badgeColor }} px-2 py-1">{{ $k->status }}</span>
                        </td>
                        <td>
                            <strong>{{ $k->items->count() }} SKU</strong> ({{ $k->items->sum('qty') }} Pcs)
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data karung tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Preview Akumulasi SKU & Qty Karung Terpilih -->
        <div class="card bg-light border-0 p-3 mb-4 d-none" id="previewCard">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="fw-bold text-dark m-0">
                    <i class="bi bi-eye text-primary me-2"></i>Preview Akumulasi SKU (<span id="countKarung" class="text-primary">0</span> Karung Dipilih)
                </h6>
                <div class="d-flex gap-3">
                    <span class="text-muted small">Total Jenis SKU: <strong id="prevTotalSku" class="text-dark">0 SKU</strong></span>
                    <span class="text-muted small">Total Keseluruhan Qty: <strong id="prevTotalQty" class="text-danger">0 Pcs</strong></span>
                </div>
            </div>

            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                <table class="table table-bordered table-sm bg-white align-middle mb-0" id="previewTable">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="8%" class="text-center">No</th>
                            <th width="30%">SKU</th>
                            <th width="20%">Akumulasi Qty</th>
                            <th width="42%">Keterangan Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Diisi via JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('pemusnahan.index') }}" class="btn btn-light px-4">Batal</a>
            <button type="submit" class="btn btn-danger px-4 fw-semibold">Ajukan Pemusnahan Karung</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.karung-checkbox');
        const selectAll = document.getElementById('selectAll');
        const previewCard = document.getElementById('previewCard');
        const searchInput = document.getElementById('searchKarung');

        // Pencarian Real-Time pada tabel karung
        searchInput.addEventListener('input', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.karung-row');

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                if (text.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        function updatePreview() {
            let selectedCheckboxes = document.querySelectorAll('.karung-checkbox:checked');
            let count = selectedCheckboxes.length;

            if (count === 0) {
                previewCard.classList.add('d-none');
                return;
            }

            document.getElementById('countKarung').innerText = count;

            let skuMap = {};
            let grandTotalQty = 0;

            // Gabungkan dan akumulasikan Qty per SKU dari semua karung yang dicentang
            selectedCheckboxes.forEach(cb => {
                let karung = JSON.parse(cb.getAttribute('data-karung'));
                if (karung.items) {
                    karung.items.forEach(item => {
                        let sku = item.sku;
                        let qty = item.qty;
                        grandTotalQty += qty;

                        let ket = item.produk ? (item.produk.keterangan || item.produk.nama_produk || '-') : '-';

                        if (!skuMap[sku]) {
                            skuMap[sku] = {
                                sku: sku,
                                qty: 0,
                                keterangan: ket
                            };
                        }
                        skuMap[sku].qty += qty;
                    });
                }
            });

            // Render ke tabel preview
            let tbody = document.querySelector('#previewTable tbody');
            tbody.innerHTML = '';
            let index = 1;

            for (let sku in skuMap) {
                let item = skuMap[sku];
                let tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="text-center">${index++}</td>
                    <td class="fw-bold text-primary">${item.sku}</td>
                    <td><strong>${item.qty} Pcs</strong></td>
                    <td>${item.keterangan}</td>
                `;
                tbody.appendChild(tr);
            }

            document.getElementById('prevTotalSku').innerText = Object.keys(skuMap).length + ' SKU Unik';
            document.getElementById('prevTotalQty').innerText = grandTotalQty + ' Pcs';
            previewCard.classList.remove('d-none');
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updatePreview);
        });

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                let visibleCheckboxes = document.querySelectorAll('.karung-row:not([style*="display: none"]) .karung-checkbox');
                visibleCheckboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updatePreview();
            });
        }
    });
</script>
@endsection