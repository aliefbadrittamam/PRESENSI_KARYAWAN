<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 🔹 Menampilkan halaman login untuk Admin
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showAdminLoginForm()
    {
        return view('auth.login');
    }

    // 🔹 Menampilkan halaman login untuk Karyawan
    public function showKaryawanLoginForm()
    {
        return view('auth.login_karyawan');
    }

    // 🔹 Proses login untuk Admin
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Pastikan user-nya role admin
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Anda tidak memiliki akses sebagai admin.']);
            }
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    // 🔹 Proses login untuk Karyawan
    public function karyawanLogin(Request $request)
    {
        $request->validate([
            'nip' => 'required',
            'password' => 'required',
        ]);

        // 1️⃣ Cari karyawan berdasarkan NIP
        $karyawan = \App\Models\Karyawan::where('nip', $request->nip)->with('user')->first();

        // 🚫 NIP tidak ditemukan
        if (!$karyawan) {
            return back()
                ->withInput()
                ->with('error_type', 'nip_not_found')
                ->with('error_message', 'NIP tidak ditemukan.');
        }

        // 🚫 Akun belum terhubung dengan user (tabel users)
        if (!$karyawan->user) {
            return back()
                ->withInput()
                ->with('error_type', 'unlinked_user')
                ->with('error_message', 'Akun ini belum terhubung dengan data login.');
        }

        // 3️⃣ Coba autentikasi berdasarkan email dari tabel users    
        if (
            Auth::attempt([
                'email' => $karyawan->user->email,
                'password' => $request->password,
            ])
        ) {
            // 4️⃣ Verifikasi role
            if (Auth::user()->role === 'user') {
                // ✅ Login sukses
                session()->flash('login_success', 'Selamat datang, ' . Auth::user()->name . '!');
                return redirect()->route('user.dashboard');
            } else {
                Auth::logout();
                return back()
                    ->withInput()
                    ->with('error_type', 'wrong_role')
                    ->with('error_message', 'Akun ini bukan karyawan.');
            }
        }

        // 🚫 Password salah
        return back()
            ->withInput()
            ->with('error_type', 'invalid_credentials')
            ->with('error_message', 'NIP atau password salah.');
    }

    // 🔹 Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.karyawan');
    }
}