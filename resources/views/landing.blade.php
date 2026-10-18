<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Retur Barang</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
        }
        .navbar-custom {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 1rem 1.5rem;
        }
        .hero-section {
            padding: 4rem 0;
            flex: 1;
            display: flex;
            align-items: center;
        }
        .card-feature {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            padding: 1.5rem;
            height: 100%;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }
        .card-feature:hover {
            transform: translateY(-4px);
            border-color: #38bdf8;
        }
        .badge-custom {
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.2);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            padding: 0.4rem 0.8rem;
            border-radius: 2rem;
            display: inline-block;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }
        .btn-custom-primary {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .btn-custom-primary:hover {
            background-color: #1d4ed8;
            color: #ffffff;
            transform: translateY(-2px);
        }
        footer {
            background-color: #1e293b;
            border-top: 1px solid #334155;
            color: #94a3b8;
            padding: 1.5rem 0;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>

    <!-- Navbar Atas -->
    <nav class="navbar navbar-custom navbar-dark">
        <div class="container d-flex justify-content-between align-items-center">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2 text-white text-decoration-none" href="#">
                <div class="bg-primary text-white p-2 rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
                <span>Retur Management</span>
            </a>
        </div>
    </nav>

    <!-- Hero Section & Fitur Utama -->
    <main class="hero-section">
        <div class="container">
            <!-- Bagian Utama (Tergabung & Berpusat Rapi) -->
            <div class="row justify-content-center align-items-center mb-5">
                <div class="col-lg-9 text-center">
                    <span class="badge-custom">
                        <i class="bi bi-shield-check me-1"></i> Sistem Logistik & Depo Terintegrasi
                    </span>
                    <h1 class="display-5 fw-bold text-white mb-3 lh-sm">
                        Manajemen Retur Barang & Pengajuan BS yang Lebih Efisien
                    </h1>
                    <p class="text-secondary fs-6 mb-4 lh-base mx-auto" style="color: #94a3b8 !important; max-width: 750px;">
                        Platform terpusat untuk memantau dashboard operasional depo, pengelolaan data karung, berita acara pemusnahan, hingga pelacakan dokumen B2B secara <em>real-time</em> dan akurat.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('login') }}" class="btn btn-custom-primary px-4 py-2 fs-6">
                            <i class="bi bi-play-circle me-1"></i> Masuk ke Aplikasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grid Fitur Unggulan -->
            <div class="row g-4 pt-4 border-top border-secondary">
                <div class="col-md-3 col-sm-6">
                    <div class="card-feature">
                        <div class="text-primary fs-3 mb-2"><i class="bi bi-clipboard-data"></i></div>
                        <h6 class="fw-bold text-white mb-1">Retur BS</h6>
                        <p class="text-secondary small m-0">Pencatatan dan rekapitulasi data barang retur harian depo secara terstruktur.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card-feature">
                        <div class="text-success fs-3 mb-2"><i class="bi bi-shop"></i></div>
                        <h6 class="fw-bold text-white mb-1">B2B Integration</h6>
                        <p class="text-secondary small m-0">Manajemen ritel modern (SAT & MIDI) lengkap dengan pemantauan status.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card-feature">
                        <div class="text-warning fs-3 mb-2"><i class="bi bi-archive"></i></div>
                        <h6 class="fw-bold text-white mb-1">Data Karung</h6>
                        <p class="text-secondary small m-0">Kontrol Pencatatan Karung.</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="card-feature">
                        <div class="text-warning fs-3 mb-2"><i class="bi bi-file-earmark-text"></i></div>
                        <h6 class="fw-bold text-white mb-1">Pengajuan</h6>
                        <p class="text-secondary small m-0">Kontrol Proses Pengajuan dengan efisien.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer Profesional -->
    <footer class="text-center">
        <div class="container">
            <p class="m-0">&copy; {{ date('Y') }} Retur Management System. Seluruh hak cipta dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>