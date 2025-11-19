<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use App\Models\User;
// ... (Tambahkan model lain jika perlu untuk reset password)

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin() {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request) {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Arahkan berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->role === 'kasir') {
                return redirect()->intended(route('kasir.pos'));
            }
        }

        // Jika gagal
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // Proses logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    // Menampilkan halaman lupa password
    public function showForgotPassword() {
        return view('auth.forgot-password');
    }

    // Kirim link reset password (Flow: User input username, sistem cari email, kirim link)
    public function sendResetLink(Request $request) {
        $request->validate(['username' => 'required|string|exists:users,username']);

        $user = User::where('username', $request->username)->first();

        if (empty($user->email)) {
            return back()->withErrors(['username' => 'Akun ini tidak memiliki email terdaftar untuk pemulihan.']);
        }
        
        // Logika untuk mengirim link reset password ke $user->email
        // Ini memerlukan setup Notifikasi dan Mailer Laravel.
        // Untuk sederhananya:
        // 1. Buat token: $token = Password::broker()->createToken($user);
        // 2. Simpan token di tabel 'password_reset_tokens'
        // 3. Kirim email ke $user->email berisi link ke route('password.reset', $token)
        
        // Placeholder untuk logika pengiriman email:
        // Mail::to($user->email)->send(new PasswordResetLinkMail($token, $user->username));

        return back()->with('status', 'Link reset password telah dikirim ke email Anda!');
    }
}

