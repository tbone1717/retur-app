@extends('layouts.admin')

@section('title', 'Form Berita Acara Pemusnahan')

@section('content')
<div class="card border-0 shadow-sm p-4">
    <div class="mb-3 pb-3 border-bottom">
        <h5 class="fw-bold text-dark m-0">
            <i class="bi bi-file-earmark-ruled text-primary me-2"></i>Buat Berita Acara Pemusnahan (BAP) Baru
        </h5>
        <span class="text-muted small">Isi formulir dokumen berita acara pemusnahan secara mandiri.</span>
    </div>

    <form action="{{ route('berita-acara.store') }}" method="POST">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Nomor BAP</label>
                <input type="text" name="nomor_bap" class="form-control form-control-sm border-primary" placeholder="Contoh: BAP/2026/001" required autofocus>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Tanggal Pemusnahan</label>
                <input type="date" name="tanggal" class="form-control form-control-sm border-primary" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Jam</label>
                <input type="text" name="jam" class="form-control form-control-sm border-primary" placeholder="Contoh: 10:00 WIB">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Depo Asal</label>
                <input type="text" name="depo" class="form-control form-control-sm border-primary" value="Bekasi Utama" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Region</label>
                <input type="text" name="region" class="form-control form-control-sm border-primary" value="JABOTABEK" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-primary small">Tempat Pemusnahan</label>
                <input type="text" name="tempat" class="form-control form-control-sm border-primary" placeholder="Lokasi pemusnahan...">
            </div>
            <div class="col-md-12">
                <label class="form-label fw-bold text-primary small">Cara Pemusnahan</label>
                <input type="text" name="cara_pemusnahan" class="form-control form-control-sm border-primary" placeholder="Contoh: dibakar / dihancurkan / dll" required>
            </div>

            <!-- Penandatangan & Saksi -->
            <div class="col-12 mt-4">
                <h6 class="fw-bold text-secondary border-bottom pb-2">Pihak Pembuat & Saksi-Saksi</h6>
            </div>
            <div class="col-md-6">
                <label class="form-label small">Nama Pembuat</label>
                <input type="text" name="nama_pembuat" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Jabatan Pembuat</label>
                <input type="text" name="jabatan_pembuat" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Nama Saksi 1</label>
                <input type="text" name="saksi_1_nama" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Jabatan Saksi 1</label>
                <input type="text" name="saksi_1_jabatan" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Nama Saksi 2</label>
                <input type="text" name="saksi_2_nama" class="form-control form-control-sm">
            </div>
            <div class="col-md-6">
                <label class="form-label small">Jabatan Saksi 2</label>
                <input type="text" name="saksi_2_jabatan" class="form-control form-control-sm">
            </div>
        </div>

        <div class="d-flex gap-2 mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">
                <i class="bi bi-save me-1"></i> Simpan Berita Acara
            </button>
            <a href="{{ route('berita-acara.index') }}" class="btn btn-sm btn-secondary px-3">Batal</a>
        </div>
    </form>
</div>
@endsection