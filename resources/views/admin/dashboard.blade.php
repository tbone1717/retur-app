<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Retur Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0b0f19;
            color: #f1f5f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        p.text-muted, .card-custom p {
            color: #94a3b8 !important; 
        }
        .admin-navbar {
            background-color: #1e293b;
            border-bottom: 1px solid #334155;
            padding: 1rem 2rem;
        }
        .card-custom {
            background-color: #1e293b;
            border: 1px solid #334155;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .text-label {
            color: #cbd5e1;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .terminal-box {
            background-color: #0f172a;
            border: 1px solid #334155;
            color: #38bdf8;
            font-family: monospace;
            font-size: 0.8rem;
            padding: 1rem;
            border-radius: 0.75rem;
            max-height: 250px;
            overflow-y: auto;
        }
    </style>
</head>
<body>

    <nav class="admin-navbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-danger text-white px-3 py-1 rounded-3 fw-bold small shadow-sm">
                <i class="bi bi-shield-lock-fill me-1"></i> Administrator
            </div>
            <div>
                <h5 class="m-0 fw-bold text-white">System Maintenance & Control Center</h5>
                <span class="small text-muted">Panel Pemeliharaan Retur Management</span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 fw-semibold">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Aplikasi Utama
            </a>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-semibold">Log Out</button>
            </form>
        </div>
    </nav>

    <div class="container py-4">
        
        @if(session('success'))
            <div class="alert alert-success bg-success bg-opacity-10 border-success text-success mb-4 rounded-3 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i> <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-10 border-danger text-danger mb-4 rounded-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i> <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="row g-4">
            
            <div class="col-lg-4">
                <div class="card card-custom p-4 h-100">
                    <h6 class="text-label mb-3 text-info"><i class="bi bi-cpu me-1"></i> Informasi Lingkungan</h6>
                    <ul class="list-unstyled small mb-4 d-flex flex-column gap-3 border-bottom border-secondary pb-3">
                        <li class="d-flex justify-content-between">
                            <span class="text-secondary">Status Aplikasi:</span> 
                            <span>
                                @if($isMaintenanceMode)
                                    <span class="badge bg-warning text-dark">Maintenance Mode</span>
                                @else
                                    <span class="badge bg-success">Live (Normal)</span>
                                @endif
                            </span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-secondary">Versi Laravel:</span> 
                            <strong class="text-white">{{ $laravelVersion }}</strong>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-secondary">Versi PHP:</span> 
                            <strong class="text-white">{{ $phpVersion }}</strong>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-secondary">Ukuran Database:</span> 
                            <strong class="text-white">{{ $databaseSize }} MB</strong>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-secondary">Database Driver:</span> 
                            <strong class="text-white">SQLite</strong>
                        </li>
                    </ul>

                    <h6 class="text-label mb-2 text-warning"><i class="bi bi-shield-exclamation me-1"></i> Maintenance Mode</h6>
                    <p class="text-muted small mb-3">Aktifkan mode pemeliharaan untuk mengunci akses pengguna saat melakukan *deployment* atau pembaruan sistem.</p>
                    <form action="{{ route('admin.toggle-maintenance') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn {{ $isMaintenanceMode ? 'btn-success' : 'btn-outline-warning' }} btn-sm w-100 fw-semibold rounded-3 py-2">
                            @if($isMaintenanceMode)
                                <i class="bi bi-play-fill me-1"></i> Nyalakan Kembali Aplikasi (Up)
                            @else
                                <i class="bi bi-pause-fill me-1"></i> Aktifkan Maintenance Mode (Down)
                            @endif
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-white mb-2"><i class="bi bi-tools text-primary me-2"></i> Panel Tindakan Pemeliharaan</h5>
                    <p class="text-muted small mb-4">Pilih tindakan operasional di bawah ini untuk menjaga performa sistem tetap optimal.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded-3 bg-dark bg-opacity-25 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><i class="bi bi-arrow-clockwise text-warning me-1"></i> Bersihkan Cache</h6>
                                    <p class="text-muted small mb-3">Menghapus cache config, rute, view, dan aplikasi secara total.</p>
                                </div>
                                <form action="{{ route('admin.clear-cache') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning btn-sm w-100 fw-semibold rounded-3">
                                        Bersihkan Cache
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded-3 bg-dark bg-opacity-25 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><i class="bi bi-database-gear text-info me-1"></i> Vacuum Database</h6>
                                    <p class="text-muted small mb-3">Mengoptimasi struktur SQLite dan mengurangi fragmentasi data.</p>
                                </div>
                                <form action="{{ route('admin.vacuum-db') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-info btn-sm w-100 fw-semibold rounded-3">
                                        Vacuum Database
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded-3 bg-dark bg-opacity-25 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><i class="bi bi-download text-success me-1"></i> Backup Database</h6>
                                    <p class="text-muted small mb-3">Unduh salinan file database `.sqlite` terbaru untuk cadangan.</p>
                                </div>
                                <a href="{{ route('admin.download-db') }}" class="btn btn-outline-success btn-sm w-100 fw-semibold rounded-3 text-decoration-none text-center">
                                    Download SQLite
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 border border-secondary rounded-3 bg-dark bg-opacity-25 h-100 d-flex flex-column justify-content-between">
                                <div>
                                    <h6 class="fw-bold text-white mb-1"><i class="bi bi-trash3 text-danger me-1"></i> Bersihkan Log</h6>
                                    <p class="text-muted small mb-3">Mengosongkan file `laravel.log` untuk menghemat ruang penyimpanan.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- KARTU MANAJEMEN PENGGUNA (USER MANAGEMENT) -->
                <!-- ========================================== -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="background-color: #1e2530; color: #fff;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div>
                            <h5 class="fw-bold mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Manajemen Pengguna Sistem</h5>
                            <p class="text-muted small mb-0">Kelola daftar akun, User ID, dan Depo akses sistem.</p>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm px-3 d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus-fill"></i> Tambah User Baru
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0" style="background-color: transparent;">
                            <thead>
                                <tr class="text-secondary border-secondary">
                                    <th class="py-3">No</th>
                                    <th class="py-3">User ID</th>
                                    <th class="py-3">Nama Lengkap</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Depo</th>
                                    <th class="py-3">Status Token Aktif</th> <!-- Kolom Indikator Token -->
                                    <th class="py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($users) == 0)
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada data user.</td>
                                    </tr>
                                @else
                                    <?php foreach($users as $index => $u): ?>
                                    <tr class="border-secondary">
                                        <td class="fw-bold">{{ $index + 1 }}</td>
                                        <td><span class="badge bg-secondary font-monospace">{{ $u->userid ?? '-' }}</span></td>
                                        <td>{{ $u->name }}</td>
                                        <td>{{ $u->email }}</td>
                                        <td><span class="badge bg-info text-dark">{{ $u->depo ?? '-' }}</span></td>
                                        
                                        <!-- Indikator Token Aktif / Kadaluwarsa -->
                                        <td>
                                            @if($u->login_token && $u->token_expires_at && \Carbon\Carbon::now()->lessThan(\Carbon\Carbon::parse($u->token_expires_at)))
                                                <div class="d-flex flex-column align-items-start gap-1">
                                                    <!-- Badge Token dengan kontras teks putih yang jelas -->
                                                    <span class="badge bg-success text-white fw-bold font-monospace px-2 py-1 shadow-sm" style="font-size: 0.85rem;">
                                                        <i class="bi bi-key-fill me-1"></i> {{ $u->login_token }}
                                                    </span>
                                                    <!-- Keterangan waktu dengan teks kuning agar terbaca jelas -->
                                                    <small class="text-warning fw-semibold" style="font-size: 0.7rem;">
                                                        Aktif s.d {{ \Carbon\Carbon::parse($u->token_expires_at)->format('H:i d/m/Y') }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary bg-opacity-25 text-muted">Tidak Ada / Kedaluwarsa</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}" title="Edit User">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                @if(auth()->id() !== $u->id)
                                                <form action="{{ route('admin.generate.token', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Buat Token Login (30 Hari) untuk user {{ $u->name }}??');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info py-0 px-2" title="Generate Token Login">
                                                        <i class="bi bi-key-fill"></i>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.user.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user {{ $u->name }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus User">
                                                        <i class="bi bi-trash-fill"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="modal fade" id="addUserModal" tabindex aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content text-dark" style="border-radius: 1rem;">
                            <form action="{{ route('admin.user.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus text-primary me-2"></i>Tambah User Baru</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">User ID</label>
                                        <input type="text" name="userid" class="form-control" placeholder="Contoh: airangga" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control" placeholder="Nama lengkap..." required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Email</label>
                                        <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Depo</label>
                                        <input type="text" name="depo" class="form-control" placeholder="Contoh: Bekasi MM" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm fw-semibold">Tambah User</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Tabel Manajemen Auto-Backup -->
                <div class="card card-custom p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white m-0"><i class="bi bi-clock-history text-success me-2"></i> Riwayat Auto-Backup</h5>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-2 py-1 small">Aktif Otomatis</span>
                    </div>
                    <p class="text-muted small mb-3">Sistem secara otomatis mencadangkan database setiap 3 hari sekali.</p>

                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle small mb-0" style="background-color: transparent;">
                            <thead>
                                <tr class="text-secondary border-bottom border-secondary">
                                    <th>Nama File Arsip</th>
                                    <th>Ukuran</th>
                                    <th>Waktu Backup</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(empty($backups))
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat file backup tersimpan.</td>
                                    </tr>
                                @else
                                    <!-- Menggunakan Native PHP Foreach -->
                                    <?php foreach($backups as$b): ?>
                                        <tr>
                                            <td><i class="bi bi-file-earmark-code text-info me-1"></i> {{ $b['filename'] }}</td>
                                            <td>{{ $b['size'] }} MB</td>
                                            <td>{{ $b['date'] }}</td>
                                            <td class="text-end">
                                                <div class="d-flex justify-content-end gap-2">
                                                    <form action="{{ route('admin.backup.restore', $b['filename']) }}" method="POST" class="m-0" onsubmit="return confirm('PERINGATAN: Database saat ini akan ditimpa dengan data dari arsip {{ $b['filename'] }}. Lanjutkan proses restore?');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-warning btn-sm py-0 px-2" title="Restore Database ini">
                                                            <i class="bi bi-arrow-counterclockwise"></i>
                                                        </button>
                                                    </form>

                                                    <a href="{{ route('admin.backup.download', $b['filename']) }}" class="btn btn-outline-info btn-sm py-0 px-2" title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>

                                                    <form action="{{ route('admin.backup.delete', $b['filename']) }}" method="POST" class="m-0" onsubmit="return confirm('Yakin ingin menghapus arsip backup ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm py-0 px-2" title="Hapus">
                                                            <i class="bi bi-trash3"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel Riwayat Aksi Pemeliharaan -->
                <div class="card card-custom p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-white m-0"><i class="bi bi-activity text-warning me-2"></i> Riwayat Aksi Pemeliharaan</h5>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-2 py-1 small">Live Log</span>
                    </div>
                    <p class="text-muted small mb-3">Catatan kronologis tindakan administratif yang dilakukan oleh Super Administrator.</p>

                    <div class="table-responsive" style="max-height: 280px; overflow-y: auto;">
                        <table class="table table-dark table-hover align-middle small mb-0" style="background-color: transparent;">
                            <thead class="sticky-top" style="background-color: #1e293b; z-index: 1;">
                                <tr class="text-secondary border-bottom border-secondary">
                                    <th>Waktu</th>
                                    <th>Admin</th>
                                    <th>Aksi</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($maintenanceLogs) == 0)
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada riwayat aksi pemeliharaan tercatat.</td>
                                    </tr>
                                @else
                                    <!-- Menggunakan Native PHP Foreach -->
                                    <?php foreach($maintenanceLogs as$log): ?>
                                        <tr>
                                            <td class="text-nowrap text-secondary">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                            <td><span class="badge bg-secondary">{{ $log->admin_name }}</span></td>
                                            <td><strong class="text-info">{{ $log->action }}</strong></td>
                                            <td>{{ $log->description }}</td>
                                        </tr>
                                    <?php endforeach; ?>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>