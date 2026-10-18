<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Retur</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0f172a;
            color: #e2e8f0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .login-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 1.5rem;
            padding: 2.25rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-label {
            color: #cbd5e1;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .form-control {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #f8fafc;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            border-radius: 0.75rem;
        }

        .form-control:focus {
            background-color: #0f172a;
            border-color: #6366f1;
            color: #f8fafc;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        .input-group-text {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #94a3b8;
            border-top-left-radius: 0.75rem;
            border-bottom-left-radius: 0.75rem;
        }

        .btn-custom {
            background-color: #6366f1;
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.75rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .btn-custom:hover {
            background-color: #4f46e5;
            transform: translateY(-1px);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
            padding: 0.35rem 0.85rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .text-subtitle {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        .back-link {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #f8fafc;
        }

        .nav-pills {
            background-color: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            padding: 4px;
        }

        .nav-pills .nav-link {
            color: #94a3b8;
            font-weight: 600;
            font-size: 0.85rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link.active {
            background-color: #6366f1;
            color: #ffffff;
        }
    </style>
</head>
<body>

    @php
        $activeTab = session('active_tab', 'password');
    @endphp

    <div class="login-card">
        <div class="text-center mb-3">
            <div class="brand-badge">
                <i class="bi bi-shield-check"></i> Internal Portal
            </div>
            <h4 class="fw-bold text-white mb-1">Retur Management</h4>
            <p class="text-subtitle mb-0">Pilih metode masuk untuk mengakses sistem.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger py-2 px-3 small rounded-3 mb-3">
                <i class="bi bi-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <!-- Navigasi Tab Pilihan -->
        <ul class="nav nav-pills nav-fill mb-3" id="loginTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 {{ $activeTab == 'password' ? 'active' : '' }}" id="password-tab" data-bs-toggle="pill" data-bs-target="#password-pane" type="button" role="tab">
                    <i class="bi bi-person-fill me-1"></i> Password
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-2 {{ $activeTab == 'token' ? 'active' : '' }}" id="token-tab" data-bs-toggle="pill" data-bs-target="#token-pane" type="button" role="tab">
                    <i class="bi bi-key-fill me-1"></i> Token
                </button>
            </li>
        </ul>

        <!-- Konten Tab -->
        <div class="tab-content" id="loginTabContent">
            
            <!-- TAB 1: LOGIN PASSWORD -->
            <div class="tab-pane fade {{ $activeTab == 'password' ? 'show active' : '' }}" id="password-pane" role="tabpanel">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="userid" class="form-control border-start-0 ps-0" value="{{ old('userid') }}" placeholder="Contoh: 0089" required autofocus autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom w-100 mb-3 shadow-sm">
                        Masuk <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>

            <!-- TAB 2: LOGIN TOKEN -->
            <div class="tab-pane fade {{ $activeTab == 'token' ? 'show active' : '' }}" id="token-pane" role="tabpanel">
                <form action="{{ route('login.token.submit') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">User ID</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-person"></i></span>
                            <input type="text" name="userid" class="form-control border-start-0 ps-0" placeholder="Contoh: 0089" required autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Token</label>
                        <div class="input-group">
                            <span class="input-group-text border-end-0"><i class="bi bi-key"></i></span>
                            <input type="text" name="token" class="form-control border-start-0 ps-0 text-uppercase fw-bold text-center" placeholder="KODE TOKEN" style="letter-spacing: 2px;" required autocomplete="off">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-custom w-100 mb-3 shadow-sm">
                        Masuk <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-2">
            <a href="{{ route('landing') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>