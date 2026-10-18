<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\B2BController;
use App\Http\Controllers\KarungController;
use App\Http\Controllers\PemusnahanController;
use App\Http\Controllers\PengajuanBsController;
use App\Http\Controllers\BeritaAcaraController;
use App\Http\Controllers\FormBSController;
use App\Http\Controllers\MutasiGsBsController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SystemController;

// Landing Page
Route::get('/', function () {
    return view('landing');
})->name('landing');

// ==========================================
// AREA LOGIN
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Login Khusus Token (Hanya untuk User / Non-Admin)
Route::get('/login-token', function () {
    return view('auth.login-token');
})->name('login.token');
Route::post('/login-token', [AuthController::class, 'processTokenLogin'])->name('login.token.submit');


// ==========================================
// AREA KHUSUS ADMINISTRATOR / MAINTENANCE
// ==========================================
Route::prefix('system-admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
    Route::post('/vacuum-db', [AdminController::class, 'vacuumDatabase'])->name('vacuum-db');
    Route::get('/download-db', [AdminController::class, 'downloadDatabase'])->name('download-db');

    // Manajemen Auto-Backup & Restore
    Route::get('/backup/download/{filename}', [AdminController::class, 'downloadSpecificBackup'])->name('backup.download');
    Route::post('/backup/restore/{filename}', [AdminController::class, 'restoreBackup'])->name('backup.restore');
    Route::delete('/backup/delete/{filename}', [AdminController::class, 'deleteBackup'])->name('backup.delete');

    Route::post('/toggle-maintenance', [AdminController::class, 'toggleMaintenance'])->name('toggle-maintenance');

    // Route Manajemen User
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');
    
    // Route Generate Token untuk User Operasional (Otomatis menjadi admin.generate.token)
    Route::post('/users/{id}/generate-token', [AdminController::class, 'generateLoginToken'])->name('generate.token');
});


// ==========================================
// AREA UTAMA APLIKASI
// ==========================================
Route::middleware(['auth', 'app.maintenance'])->group(function () {
    
    // Home Page
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Retur BS
    Route::prefix('retur')->name('retur.')->group(function () {
        Route::get('/dashboard', [ReturController::class, 'dashboard'])->name('dashboard');
        Route::get('/input', [ReturController::class, 'input'])->name('input');
        Route::post('/input', [ReturController::class, 'store'])->name('store');
        
        Route::get('/history', [ReturController::class, 'historyIndex'])->name('history.index');
        Route::get('/history/{bulan}', [ReturController::class, 'historyTanggal'])->name('history.tanggal');
        Route::get('/history/detail/{tanggal}', [ReturController::class, 'historyDetail'])->name('history.detail');

        Route::get('/edit/{id}', [ReturController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ReturController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [ReturController::class, 'destroy'])->name('destroy');
        
        Route::get('/export/excel', [ReturController::class, 'exportExcel'])->name('export.excel');
        Route::get('/export/excel/{bulan?}', [ReturController::class, 'exportExcel'])->name('export.excel');
    });

    // B2B Menu
    Route::prefix('b2b')->name('b2b.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [B2BController::class, 'dashboard'])->name('dashboard');
        Route::get('/input', [B2BController::class, 'input'])->name('input');
        Route::post('/input', [B2BController::class, 'store'])->name('store');
        
        Route::get('/history', [B2BController::class, 'historyIndex'])->name('history.index');
        Route::get('/history/{bulan}', [B2BController::class, 'historyTanggal'])->name('history.tanggal');
        Route::get('/history/detail/{tanggal}', [B2BController::class, 'historyDetail'])->name('history.detail');
        
        Route::get('/edit/{id}', [B2BController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [B2BController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [B2BController::class, 'destroy'])->name('destroy');

        Route::get('/sat', [B2BController::class, 'indexSat'])->name('sat');
        Route::get('/midi', [B2BController::class, 'indexMidi'])->name('midi');
        
        Route::put('/status/{id}', [B2BController::class, 'updateStatus'])->name('status.update');
        Route::get('/show/{id}', [B2BController::class, 'show'])->name('show');
        
        Route::get('/export/excel/{bulan?}', [B2BController::class, 'exportExcel'])->name('export.excel');
    });

    // Data Karung
    Route::prefix('karung')->name('karung.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [KarungController::class, 'index'])->name('index');
        Route::get('/input', [KarungController::class, 'input'])->name('input');
        Route::post('/store', [KarungController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KarungController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KarungController::class, 'update'])->name('update');
    });

    // Pemusnahan
    Route::prefix('pemusnahan')->name('pemusnahan.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [PemusnahanController::class, 'index'])->name('index');
        Route::get('/input', [PemusnahanController::class, 'input'])->name('input');
        Route::post('/store', [PemusnahanController::class, 'store'])->name('store');
        Route::delete('/destroy/{id}', [PemusnahanController::class, 'destroy'])->name('destroy');
        Route::get('/pdf', [PemusnahanController::class, 'pdf'])->name('pdf');
    });

    // Pengajuan BS
    Route::get('/form-bs', [PengajuanBsController::class, 'index'])->name('form.bs.index');
    Route::get('/pengajuan-bs/create', [PengajuanBsController::class, 'create'])->name('pengajuan-bs.create');
    Route::post('/pengajuan-bs', [PengajuanBsController::class, 'store'])->name('pengajuan-bs.store');
    
    Route::get('/pengajuan-bs/export-excel/{nomor_pengajuan}', [PengajuanBsController::class, 'exportExcel'])
         ->name('pengajuan-bs.export.excel')
         ->where('nomor_pengajuan', '.*');

    Route::get('/pengajuan-bs/export-pdf/{nomor_pengajuan}', [PengajuanBsController::class, 'exportPdf'])
         ->name('pengajuan-bs.export.pdf')
         ->where('nomor_pengajuan', '.*');

    Route::get('/pengajuan-bs/{nomor_pengajuan}/edit', [PengajuanBsController::class, 'edit'])
         ->name('pengajuan-bs.edit')
         ->where('nomor_pengajuan', '.*');

    Route::put('/pengajuan-bs/{nomor_pengajuan}', [PengajuanBsController::class, 'update'])
         ->name('pengajuan-bs.update')
         ->where('nomor_pengajuan', '.*');

    Route::get('/pengajuan-bs/{nomor_pengajuan}', [PengajuanBsController::class, 'show'])
         ->name('pengajuan-bs.show')
         ->where('nomor_pengajuan', '.*');

    Route::delete('/pengajuan-bs/{nomor_pengajuan}', [PengajuanBsController::class, 'destroy'])
         ->name('pengajuan-bs.destroy')
         ->where('nomor_pengajuan', '.*');

    // Berita Acara Pemusnahan (BAP) Mandiri
    Route::get('/berita-acara', [BeritaAcaraController::class, 'index'])->name('berita-acara.index');
    Route::get('/berita-acara/create', [BeritaAcaraController::class, 'create'])->name('berita-acara.create');
    Route::post('/berita-acara', [BeritaAcaraController::class, 'store'])->name('berita-acara.store');
    Route::delete('/berita-acara/{id}', [BeritaAcaraController::class, 'destroy'])->name('berita-acara.destroy');
    
    Route::get('/berita-acara/export-excel/{id}', [BeritaAcaraController::class, 'exportExcel'])->name('berita-acara.export.excel');
    Route::get('/berita-acara/export-pdf/{id}', [BeritaAcaraController::class, 'exportPdf'])->name('berita-acara.export.pdf');

    // Mutasi GS to BS (Damage Rate)
    Route::get('/mutasi-gs-bs', [MutasiGsBsController::class, 'index'])->name('mutasi-gs-bs.index');
    Route::get('/mutasi-gs-bs/create', [MutasiGsBsController::class, 'create'])->name('mutasi-gs-bs.create');
    Route::post('/mutasi-gs-bs', [MutasiGsBsController::class, 'store'])->name('mutasi-gs-bs.store');

    Route::get('/mutasi-gs-bs/export-excel/{nomor_mutasi}', [MutasiGsBsController::class, 'exportExcel'])
         ->name('mutasi-gs-bs.export.excel')
         ->where('nomor_mutasi', '.*');

    Route::get('/mutasi-gs-bs/export-pdf/{nomor_mutasi}', [MutasiGsBsController::class, 'exportPdf'])
         ->name('mutasi-gs-bs.export.pdf')
         ->where('nomor_mutasi', '.*');

    Route::get('/mutasi-gs-bs/{nomor_mutasi}/edit', [MutasiGsBsController::class, 'edit'])
         ->name('mutasi-gs-bs.edit')
         ->where('nomor_mutasi', '.*');

    Route::put('/mutasi-gs-bs/{nomor_mutasi}', [MutasiGsBsController::class, 'update'])
         ->name('mutasi-gs-bs.update')
         ->where('nomor_mutasi', '.*');

    Route::get('/mutasi-gs-bs/{nomor_mutasi}', [MutasiGsBsController::class, 'show'])
         ->name('mutasi-gs-bs.show')
         ->where('nomor_mutasi', '.*');

    Route::delete('/mutasi-gs-bs/{nomor_mutasi}', [MutasiGsBsController::class, 'destroy'])
         ->name('mutasi-gs-bs.destroy')
         ->where('nomor_mutasi', '.*');

    // Data Produk
    Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
    Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
    Route::put('/produk/{id}', [ProdukController::class, 'update'])->name('produk.update');
    Route::post('/produk/import', [ProdukController::class, 'import'])->name('produk.import');
    Route::get('/produk/export', [ProdukController::class, 'export'])->name('produk.export');
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    // System
    Route::get('/system', [SystemController::class, 'index'])->name('system.index');
});