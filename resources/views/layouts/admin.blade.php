<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Retur Management System</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        /* ==========================================
            GLOBAL THEME CONFIGURATION
            ========================================== */
        :root {
            /* Light Theme */
            --bg-body: #f8fafc;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --primary-color: #4f46e5;
            --sidebar-hover: #f1f5f9;
            --table-header: #f8fafc;
            --input-bg: #ffffff;
            --input-text: #1e293b;
        }

        [data-theme="dark"] {
            /* Dark Theme */
            --bg-body: #0f172a;
            --bg-sidebar: #1e293b;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --primary-color: #6366f1;
            --sidebar-hover: #334155;
            --table-header: #1e293b;
            --input-bg: #0f172a;
            --input-text: #f8fafc;
        }

        [data-theme="material"] {
            /* Material Theme (Teal & Slate Dark Sidebar) */
            --bg-body: #f4f6f8;
            --bg-sidebar: #263238;
            --bg-card: #ffffff;
            --text-main: #212529;
            --text-muted: #6c757d;
            --border-color: #cfd8dc;
            --primary-color: #00897b;
            --sidebar-hover: #37474f;
            --table-header: #eceff1;
            --input-bg: #ffffff;
            --input-text: #212529;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            transition: background-color 0.3s ease, color 0.3s ease;
            overflow-x: hidden;
        }

        /* Sidebar Styling (Responsif Desktop & Mobile) */
        .sidebar {
            width: 270px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 1045;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border-color);
            min-height: 70px;
        }

        /* Theme Switcher */
        .theme-switcher {
            display: flex;
            background: var(--sidebar-hover);
            padding: 0.25rem;
            border-radius: 50rem;
            gap: 0.25rem;
            margin: 1rem 1rem 0.5rem 1rem;
        }

        .theme-btn {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-size: 0.73rem;
            font-weight: 600;
            padding: 0.3rem 0;
            border-radius: 50rem;
            transition: all 0.2s;
        }

        .theme-btn.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }

        .sidebar-menu {
            flex: 1;
            overflow-y: auto;
            padding: 0.75rem 1rem;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-menu::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.88rem;
            padding: 0.65rem 0.85rem;
            border-radius: 0.75rem;
            margin-bottom: 0.2rem;
            transition: all 0.2s ease;
        }

        .nav-link i {
            font-size: 1.05rem;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: var(--text-main);
        }

        .nav-link.active {
            background-color: var(--primary-color);
            color: #ffffff !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-sidebar);
        }

        /* Main Content Desktop Default */
        .main-content {
            margin-left: 270px;
            padding: 2.5rem;
            min-height: 100vh;
            transition: margin 0.3s ease;
        }

        /* Top Mobile Navbar (Hanya tampil di layar HP/Tablet) */
        .mobile-navbar {
            background-color: var(--bg-sidebar);
            border-bottom: 1px solid var(--border-color);
            display: none;
            padding: 0.75rem 1rem;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        /* Media Query untuk Responsivitas Layar Mobile (< 992px) */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0 !important;
                padding: 1.25rem;
            }
            .mobile-navbar {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
        }

        /* Overlay gelap saat sidebar mobile terbuka */
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }
        .sidebar-backdrop.show {
            display: block;
        }

        /* Global Override untuk Bootstrap Elements agar mengikuti Tema */
        .card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            color: var(--text-main) !important;
        }

        .table {
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }

        .table > :not(caption) > * > * {
            background-color: var(--bg-card) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }

        .table thead th {
            background-color: var(--table-header) !important;
            color: var(--text-main) !important;
            border-color: var(--border-color) !important;
        }

        .form-control, .form-select {
            background-color: var(--input-bg) !important;
            color: var(--input-text) !important;
            border-color: var(--border-color) !important;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }
    </style>
</head>
<body>

    <!-- Top Navbar Khusus Mobile -->
    <div class="mobile-navbar">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center p-2" id="sidebarToggle" type="button">
                <i class="bi bi-list fs-5"></i>
            </button>
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIyIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWJveGVzIHByZXZpZXctaWNvbiI+PHBhdGggZD0iTTIuOTcgMTIuOTJBMiAyIDAgMCAwIDIgMTQuNjN2My4yNGEyIDIgMCAwIDAgLjk3IDEuNzFsMyAxLjhhMiAyIDAgMCAwIDIuMDYgMEwxMiAxOXYtNS41bC01LTMtNC4wMyAyLjQyWiIvPjxwYXRoIGQ9Im03IDE2LjUtNC43NC0yLjg1Ii8+PHBhdGggZD0ibTcgMTYuNSA1LTMiLz48cGF0aCBkPSJNNyAxNi41djVuMTciLz48cGF0aCBkPSJNMTIgMTMuNVYxOWwzLjk3IDIuMzhhMiAyIDAgMCAwIDIuMDYgMGwzLTEuOGEyIDIgMCAwIDAgLjk3LTEuNzF2LTMuMjRhMiAyIDAgMCAwLS45Ny0xLjcxTDE3IDEwLjVsLTUgM1oiLz48cGF0aCBkPSJtMTcgMTYuNS01LTMiLz48cGF0aCBkPSJtMTcgMTYuNSA0Ljc0LTIuODUiLz48cGF0aCBkPSJNMTcgMTYuNXY1LjE3Ii8+PHBhdGggZD0iTTcuOTcgNC40MkEyIDIgMCAwIDAgNyA2LjEzdjQuMzdsNSAzIDUtM1Y2LjEzYTIgMiAwIDAgMC0uOTctMS43MWwtMy0xLjhhMiAyIDAgMCAwLTIuMDYgMGwtMyAxLjhaIi8+PHBhdGggZD0iTTEyIDggNy4yNiA1LjE1Ii8+PHBhdGggZD0ibTEyIDggNC43NC0yLjg1Ii8+PHBhdGggZD0iTTEyIDEzLjVWOCIvPjwvc3ZnPg==" class="w-6 h-6 text-indigo-600" alt="Logo Retur">
            <span class="fw-bold fs-6" style="color: var(--text-main);">RETUR APP</span>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1" style="font-size: 0.7rem;">
            {{ Auth::user()->depo ?? 'Depo' }}
        </span>
    </div>

    <!-- Backdrop untuk Mobile Sidebar -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebarElement">
        <div class="sidebar-brand">
            <div class="bg-primary text-white rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                <i class="bi bi-box-seam"></i>
            </div>
            <span>RETUR SYSTEM</span>
            <button class="btn-close ms-auto d-lg-none" id="sidebarClose" type="button" aria-label="Close"></button>
        </div>

        <!-- Theme Switcher -->
        <div class="theme-switcher">
            <button class="theme-btn" onclick="setTheme('light')">Light</button>
            <button class="theme-btn" onclick="setTheme('dark')">Dark</button>
            <button class="theme-btn" onclick="setTheme('material')">Material</button>
        </div>

        <!-- Scrollable Menu -->
        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('retur.dashboard') }}" class="nav-link {{ request()->routeIs('retur.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-return-left"></i> Retur BS
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('b2b.dashboard') }}" class="nav-link {{ request()->routeIs('b2b.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i> B2B
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('karung.index') }}" class="nav-link {{ request()->routeIs('karung.*') ? 'active' : '' }}">
                        <i class="bi bi-basket2"></i> Data Karung
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pemusnahan.index') }}" class="nav-link {{ request()->routeIs('pemusnahan.*') ? 'active' : '' }}">
                        <i class="bi bi-trash3"></i> Pemusnahan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('form.bs.index') }}" class="nav-link {{ request()->routeIs('form.bs.*') || request()->routeIs('pengajuan-bs.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text"></i> Pengajuan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mutasi-gs-bs.index') }}" class="nav-link {{ request()->routeIs('mutasi-gs-bs.*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-repeat"></i> Mutasi GS to BS
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('berita-acara.index') }}" class="nav-link {{ request()->routeIs('berita-acara.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-richtext"></i> Form Berita Acara
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('produk.index') }}" class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Data Produk
                    </a>
                </li>
            </ul>
        </div>

        <!-- Pinned Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="p-2 rounded-4 border mb-2" style="background-color: var(--sidebar-hover); border-color: var(--border-color) !important;">
                <div class="d-flex align-items-center mb-2 px-1">
                    <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold me-2" style="width: 34px; height: 34px; font-size: 0.85rem; min-width: 34px;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h6 class="mb-0 fw-bold text-truncate" style="font-size: 0.8rem; color: var(--text-main);">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-0 fw-semibold d-block text-start mt-1" style="font-size: 0.63rem;">
                            <i class="bi bi-geo-alt-fill me-1"></i> {{ Auth::user()->depo ?? '-' }}
                        </span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-0 fw-semibold d-block text-start mt-1" style="font-size: 0.63rem;">
                            <i class="bi bi-map-fill me-1"></i> {{ Auth::user()->region ?? 'JABOTABEK' }}
                        </span>
                    </div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-semibold rounded-pill py-1" style="font-size: 0.75rem;">
                    <i class="bi bi-box-arrow-right me-1"></i> Log Out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
        <!-- WIDGET TIMER TOKEN AKTIF (Tampil Global di Semua Halaman) -->
        @if(session('login_via_token'))
            <div class="alert alert-info bg-info bg-opacity-10 border-info text-info py-2 px-3 small rounded-3 d-flex align-items-center justify-content-between mb-4 shadow-sm">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-stopwatch-fill fs-5"></i>
                    <div>
                        <span class="fw-semibold">Token Aktif:</span> 
                        <span id="tokenCountdown" class="fw-bold font-monospace text-warning ms-1">Loading...</span>
                    </div>
                </div>
                <span class="text-muted small">Otomatis berakhir saat waktu habis</span>
            </div>

            <script>
                const countDownDate = {{ session('token_expires_timestamp', 0) }};

                const x = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = countDownDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    const countdownEl = document.getElementById("tokenCountdown");
                    if (countdownEl) {
                        if (distance < 0) {
                            clearInterval(x);
                            countdownEl.innerHTML = "KEDALUWARSA";
                            alert("Sesi token Anda telah habis. Anda akan dikeluarkan dari sistem.");
                            window.location.href = "{{ route('logout') }}";
                        } else {
                            countdownEl.innerHTML = `${days} hari ${hours}j ${minutes}m ${seconds}d`;
                        }
                    }
                }, 1000);
            </script>
        @endif
        <!-- AKHIR WIDGET TIMER GLOBAL -->

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS Bundle & Theme Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Switcher Logic
        function setTheme(theme) {
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('app_theme', theme);
            
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.remove('active');
                if(btn.textContent.toLowerCase() === theme) {
                    btn.classList.add('active');
                }
            });
        }

        const savedTheme = localStorage.getItem('app_theme') || 'light';
        setTheme(savedTheme);

        // Mobile Sidebar Toggle Logic
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarElement = document.getElementById('sidebarElement');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');

        function toggleSidebar() {
            sidebarElement.classList.toggle('show');
            sidebarBackdrop.classList.toggle('show');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
        if (sidebarBackdrop) sidebarBackdrop.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>