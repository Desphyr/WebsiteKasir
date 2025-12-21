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

    // Kirim link reset password
    public function sendResetLink(Request $request) {
        $request->validate(['username' => 'required|string|exists:users,username']);

        $user = User::where('username', $request->username)->first();

        if (empty($user->email)) {
            return back()->withErrors(['username' => 'Akun ini tidak memiliki email terdaftar untuk pemulihan.']);
        }
        
        // Generate token
        $token = app('auth.password.broker')->createToken($user);
        
        // Send custom notification
        $user->notify(new \App\Notifications\ResetPasswordNotification($token));

        return back()->with('status', 'Link reset password telah dikirim ke email Anda!');
    }

    // Menampilkan form reset password
    public function showResetForm(Request $request, $token) {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Proses reset password
    public function reset(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Password berhasil direset! Silakan login dengan password baru.');
        }

        return back()->withErrors(['email' => 'Token reset password tidak valid atau sudah kedaluwarsa.']);
    }
}

