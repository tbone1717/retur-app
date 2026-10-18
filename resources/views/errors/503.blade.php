<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .maintenance-card {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1.25rem;
            padding: 3rem 2.5rem;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .icon-box {
            width: 70px;
            height: 70px;
            background: rgba(245, 158, 11, 0.1);
            color: #f59e0b;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 1.5rem auto;
        }
        .text-desc {
            color: #94a3b8;
            font-size: 0.9rem;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center px-3">
        <div class="maintenance-card">
            <div class="icon-box">
                <i class="bi bi-tools"></i>
            </div>
            
            <h4 class="fw-bold text-white mb-2">Sistem Sedang Pemeliharaan</h4>
            <p class="text-desc mb-4">
                Mohon maaf atas ketidaknyamanannya. Saat ini sedang dilakukan pemeliharaan berkala dan peningkatan performa sistem <strong>Retur Management</strong>.
            </p>

            <div class="p-3 rounded-3 bg-dark bg-opacity-50 border border-secondary text-start small mb-4">
                <div class="d-flex align-items-center gap-2 text-info mb-1 fw-semibold">
                    <i class="bi bi-info-circle-fill"></i> Informasi Akses:
                </div>
                <span class="text-secondary" style="color: #cbd5e1 !important;">
                    Silakan coba kembali beberapa saat lagi.
                </span>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm rounded-pill w-100 fw-semibold py-2">
                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                </button>
            </form>
        </div>
    </div>
</body>
</html>