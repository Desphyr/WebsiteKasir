<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <-- DI SINI SAYA TAMBAHKAN
use App\Http\Controllers\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProfileController;

// Kasir Controllers
use App\Http\Controllers\Cashier\POSController;

// Halaman utama "Pintar" (Memperbaiki redirect loop)
// <-- BLOK INI SAYA GANTI SEPENUHNYA
Route::get('/', function () {
    if (Auth::guest()) {
        return redirect()->route('login');
    }
    
    if (Auth::user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    
    if (Auth::user()->role === 'kasir') {
        return redirect()->route('kasir.pos');
    }
    
    // Fallback jika user punya role aneh
    Auth::logout();
    return redirect()->route('login');
});

// --- Rute Autentikasi ---
Route::controller(AuthController::class)->group(function () {
    Route::get('login', 'showLogin')->name('login')->middleware('guest');
    Route::post('login', 'login')->middleware('guest');
    Route::post('logout', 'logout')->name('logout')->middleware('auth');
    
    // Lupa Password
    Route::get('forgot-password', 'showForgotPassword')->name('password.request')->middleware('guest');
    Route::post('forgot-password', 'sendResetLink')->name('password.username')->middleware('guest');
    // Route::get('reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset')->middleware('guest');
    // Route::post('reset-password', [AuthController::class, 'reset'])->name('password.update')->middleware('guest');
});


// --- Rute untuk ADMIN ---
// <-- SAYA TAMBAHKAN \App\Http\Middleware\PreventBackHistory::class
Route::middleware(['auth', 'role:admin', \App\Http\Middleware\PreventBackHistory::class])
    ->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Menu (CRUD)
    Route::resource('menu', MenuController::class);

    // Manajemen Kategori (CRUD)
    Route::resource('kategori', CategoryController::class);

    // Laporan Penjualan
    Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('laporan/export', [ReportController::class, 'export'])->name('laporan.export');

    // Catat Pengeluaran (CRUD)
    Route::resource('pengeluaran', ExpenseController::class);

    // Manajemen Staf (CRUD)
    Route::resource('staf', StaffController::class);

    // Profil Admin
    Route::get('profil', [ProfileController::class, 'edit'])->name('profil.edit');
    Route::put('profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::put('profil/password', [ProfileController::class, 'updatePassword'])->name('profil.password');

});


// --- Rute untuk KASIR ---
// <-- SAYA TAMBAHKAN \App\Http\Middleware\PreventBackHistory::class
Route::middleware(['auth', 'role:kasir', \App\Http\Middleware\PreventBackHistory::class])
    ->prefix('kasir')->name('kasir.')->group(function () {
    
    // Halaman POS Utama
    Route::get('pos', [POSController::class, 'index'])->name('pos');
    
    // Proses Transaksi
    Route::post('pos/bayar', [POSController::class, 'processPayment'])->name('pos.bayar');

    // History Transaksi
    Route::get('pos/history', [POSController::class, 'history'])->name('pos.history');
    Route::get('pos/history/{id}', [POSController::class, 'showTransactionDetail'])->name('pos.history.show');
    
    // API untuk data recent (optional)
    Route::get('pos/recent', [POSController::class, 'getRecentTransactions'])->name('pos.recent');

});
