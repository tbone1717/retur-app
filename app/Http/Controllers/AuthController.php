<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        // 1. Buat Kunci Rate Limiting berdasarkan IP Pengguna
        $key = 'login-attempts:' . $request->ip();

        // 2. Cek apakah IP ini sudah gagal login 5x berturut-turut
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('active_tab', 'password')->withErrors([
                'userid' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."
            ])->onlyInput('userid');
        }

        $credentials = $request->validate([
            'userid' => ['required', 'string'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['userid' => $credentials['userid'], 'password' => $credentials['password']])) {
            // Login Berhasil: Reset / bersihkan riwayat percobaan gagal
            RateLimiter::clear($key);

            $request->session()->regenerate();

            if (Auth::user()->is_admin) {
                return redirect()->intended('/system-admin/dashboard');
            }

            return redirect()->intended('/home');
        }

        // Login Gagal: Catat percobaan gagal (Blokir 300 detik jika sudah 5x)
        RateLimiter::hit($key, 300);

        return back()->with('active_tab', 'password')->withErrors([
            'userid' => 'User ID atau password yang Anda masukkan salah.',
        ])->onlyInput('userid');
    }

    public function processTokenLogin(Request $request)
    {
        // 1. Rate Limiting khusus percobaan Login Token berdasarkan IP
        $key = 'token-login-attempts:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('active_tab', 'token')->withErrors([
                'token' => "Terlalu banyak percobaan token. Silakan coba lagi dalam {$seconds} detik."
            ]);
        }

        $request->validate([
            'userid' => 'required|string',
            'token' => 'required'
        ]);

        $user = User::where('userid', $request->userid)->first();

        // 1. Validasi User & Kecocokan Token
        if (!$user || empty($user->login_token) || $user->login_token !== strtoupper($request->token)) {
            RateLimiter::hit($key, 300);
            return back()->with('active_tab', 'token')->withErrors(['token' => 'Token tidak valid atau User ID salah.']);
        }

        // 2. Validasi Batas Waktu Token (Kedaluwarsa)
        if (!$user->token_expires_at || Carbon::now()->greaterThan($user->token_expires_at)) {
            RateLimiter::hit($key, 300);
            return back()->with('active_tab', 'token')->withErrors(['token' => 'Token sudah kedaluwarsa. Silakan minta Admin generate ulang.']);
        }

        // 3. Kebijakan Keamanan: Hanya User Non-Admin
        if ($user->is_admin) {
            RateLimiter::hit($key, 300);
            return back()->with('active_tab', 'token')->withErrors(['token' => 'Akses ditolak. Administrator sistem tidak diizinkan menggunakan login token.']);
        }

        // Berhasil login via Token: Bersihkan hitungan pembatasan
        RateLimiter::clear($key);

        $expiresTimestamp = Carbon::parse($user->token_expires_at)->timestamp * 1000;

        Auth::login($user);
        $request->session()->regenerate();

        session([
            'login_via_token' => true,
            'token_expires_timestamp' => $expiresTimestamp
        ]);

        return redirect()->intended('/home')->with('success', 'Berhasil masuk menggunakan Token Akses!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}