<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use App\Models\MaintenanceLog;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    private function recordLog($action, $description)
    {
        MaintenanceLog::create([
            'admin_name'  => auth()->user()->name ?? 'Administrator',
            'action'      => $action,
            'description' => $description,
        ]);

        $excessIds = MaintenanceLog::latest()->get()->skip(10)->pluck('id');
        if ($excessIds->isNotEmpty()) {
            MaintenanceLog::whereIn('id', $excessIds)->delete();
        }
    }

    public function index()
    {
        $laravelVersion = app()->version();
        $phpVersion = phpversion();
        
        $dbPath = database_path('database.sqlite');
        $databaseSize = file_exists($dbPath) ? round(filesize($dbPath) / 1024 / 1024, 2) : 0;
        
        $isMaintenanceMode = Cache::has('app_custom_maintenance');

        $users = User::all();

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backups = [];
        if (File::exists($backupDir)) {
            foreach (File::files($backupDir) as $file) {
                $backups[] = [
                    'path'     => $file->getPathname(),
                    'filename' => $file->getFilename(),
                    'size'     => round($file->getSize() / 1024 / 1024, 2),
                    'date'     => date('Y-m-d H:i:s', $file->getMTime()),
                    'mtime'    => $file->getMTime(),
                ];
            }
            usort($backups, fn($a, $b) => $b['mtime'] <=> $a['mtime']);
        }

        $needNewBackup = false;
        if (empty($backups)) {
            $needNewBackup = true;
        } else {
            if ((time() - $backups[0]['mtime']) >= (3 * 24 * 60 * 60)) {
                $needNewBackup = true;
            }
        }

        if (file_exists($dbPath) && $needNewBackup) {
            $newBackupName = 'backup_' . date('Y-m-d_H-i-s') . '.sqlite';
            $newBackupPath = $backupDir . '/' . $newBackupName;
            
            File::copy($dbPath, $newBackupPath);
            array_unshift($backups, [
                'path'     => $newBackupPath,
                'filename' => $newBackupName,
                'size'     => round(filesize($dbPath) / 1024 / 1024, 2),
                'date'     => date('Y-m-d H:i:s'),
                'mtime'    => time(),
            ]);
            $this->recordLog('Auto Backup', 'Sistem membuat salinan cadangan otomatis: ' . $newBackupName);
        }

        while (count($backups) > 10) {
            $oldest = array_pop($backups);
            if (file_exists($oldest['path'])) {
                File::delete($oldest['path']);
            }
        }

        $maintenanceLogs = MaintenanceLog::latest()->take(10)->get();

        return view('admin.dashboard', compact(
            'laravelVersion', 'phpVersion', 'databaseSize', 'isMaintenanceMode', 'backups', 'maintenanceLogs', 'users'
        ));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'userid'   => 'required|string|max:255|unique:users,userid',
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'depo'     => 'required|string|max:255',
            'password' => 'required|min:6',
        ]);

        User::create([
            'userid'   => $request->userid,
            'name'     => $request->name,
            'email'    => $request->email,
            'depo'     => $request->depo,
            'password' => Hash::make($request->password),
        ]);

        $this->recordLog('Tambah User', 'Menambahkan user baru: ' . $request->userid);
        return back()->with('success', 'User baru berhasil ditambahkan.');
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'userid' => 'required|string|max:255|unique:users,userid,' . $user->id,
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'depo'   => 'required|string|max:255',
        ]);

        $data = [
            'userid' => $request->userid,
            'name'   => $request->name,
            'email'  => $request->email,
            'depo'   => $request->depo,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $this->recordLog('Edit User', 'Memperbarui data user: ' . $user->userid);

        return back()->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        if (auth()->id() == $id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun yang sedang Anda gunakan.');
        }

        $user = User::findOrFail($id);
        $email = $user->email;
        $user->delete();

        $this->recordLog('Hapus User', 'Menghapus user: ' . $email);
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function generateLoginToken($id)
    {
        $user = User::findOrFail($id);
        
        $plainToken = strtoupper(Str::random(6)); 

        $user->update([
            'login_token' => $plainToken,
            'token_expires_at' => Carbon::now()->addDays(30) 
        ]);

        $this->recordLog('Generate Token', 'Membuat token login operasional untuk user: ' . $user->email);

        return back()->with('success', "Token berhasil dibuat untuk {$user->name}. Berikan kode ini: " . $plainToken);
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        $this->recordLog('Clear Cache', 'Membersihkan cache sistem, config, route, dan view secara total.');
        return back()->with('success', 'Cache sistem berhasil dibersihkan total!');
    }

    public function vacuumDatabase()
    {
        try {
            DB::statement('VACUUM;');
            $this->recordLog('Vacuum Database', 'Mengoptimasi struktur basis data SQLite.');
            return back()->with('success', 'Database SQLite berhasil di-vacuum dan dioptimasi ulang!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengoptimasi database: ' . $e->getMessage());
        }
    }

    public function downloadDatabase()
    {
        $this->recordLog('Manual Backup', 'Mengunduh file database utama secara manual.');
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            return response()->download($dbPath, 'database_manual_backup_' . date('Y-m-d_H-i-s') . '.sqlite');
        }
        return back()->with('error', 'File database SQLite tidak ditemukan.');
    }

    public function downloadSpecificBackup($filename)
    {
        $this->recordLog('Download Arsip', 'Mengunduh file arsip backup: ' . $filename);
        $filePath = storage_path('app/backups/' . basename($filename));
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        return back()->with('error', 'File arsip backup tidak ditemukan.');
    }

    public function restoreBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . basename($filename));
        $dbPath = database_path('database.sqlite');

        if (file_exists($backupPath)) {
            File::copy($backupPath, $dbPath);
            $this->recordLog('Restore Database', 'Memulihkan sistem dari arsip backup: ' . $filename);
            return back()->with('success', 'Database berhasil dipulihkan dari arsip ' . $filename . '!');
        }
        
        return back()->with('error', 'File arsip backup tidak ditemukan.');
    }

    public function deleteBackup($filename)
    {
        $filePath = storage_path('app/backups/' . basename($filename));
        if (file_exists($filePath)) {
            File::delete($filePath);
            $this->recordLog('Hapus Arsip', 'Menghapus file arsip backup lama: ' . $filename);
            return back()->with('success', 'Arsip backup ' . $filename . ' berhasil dihapus.');
        }
        return back()->with('error', 'File arsip backup tidak ditemukan.');
    }

    public function toggleMaintenance()
    {
        if (Cache::has('app_custom_maintenance')) {
            Cache::forget('app_custom_maintenance');
            $this->recordLog('Live Mode', 'Membuka kembali akses sistem');
            return back()->with('success', 'Aplikasi telah dibuka kembali (Live Mode).');
        } else {
            Cache::put('app_custom_maintenance', true);
            $this->recordLog('Maintenance Mode', 'Mengaktifkan Maintenance');
            return back()->with('success', 'Maintenance Mode aktif!');
        }
    }
}