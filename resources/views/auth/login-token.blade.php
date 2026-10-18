<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login via Token - Retur Management</title>
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
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        }
        .form-control {
            background-color: #0f172a;
            border-color: #334155;
            color: #f1f5f9;
        }
        .form-control:focus {
            background-color: #0f172a;
            border-color: #38bdf8;
            color: #f1f5f9;
            box-shadow: none;
        }
        p.text-muted {
            color: #94a3b8 !important;
        }
    </style>
</head>
<body>

    <div class="container" style="max-width: 420px;">
        <div class="card card-custom p-4">
            <div class="text-center mb-4">
                <div class="bg-info bg-opacity-10 text-info rounded-circle d-inline-flex p-3 mb-2">
                    <i class="bi bi-key-fill fs-3"></i>
                </div>
                <h4 class="fw-bold text-white">Login via Token</h4>
                <p class="text-muted small">Masukkan email terdaftar dan kode token yang diberikan oleh Administrator.</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger small py-2 mb-3 rounded-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.token.submit') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Email Karyawan / User</label>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required autofocus>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">Kode Token (6 Karakter)</label>
                    <input type="text" name="token" class="form-control text-uppercase fw-bold text-center" placeholder="KODE TOKEN" style="letter-spacing: 2px;" required autocomplete="off">
                </div>

                <button type="submit" class="btn btn-info w-100 fw-semibold py-2 mb-3 text-dark">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Aplikasi
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none small text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Login Password Utama
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>