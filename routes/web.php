<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Mahasiswa\DashboardController as MahasiswaDashboardController;
use App\Http\Controllers\Kalab\DashboardController as KalabDashboardController;
use App\Http\Controllers\Dosen\DashboardController as DosenDashboardController;

// Landing Page (Public)
Route::get('/', function () {
    return view('landing-page');
})->name('home');

// Login & Register Page
Route::get('/login-new', function () {
    return view('login-register');
})->name('login-new');

// Authentication Routes
Route::get('/login', function () {
    return redirect()->route('login-new');
})->name('login'); // Laravel auth middleware redirects here by default

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Telegram Webhook (no CSRF, public endpoint for Telegram API)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

// Telegram API Routes (authenticated users)
Route::middleware('auth')->prefix('telegram')->name('telegram.')->group(function () {
    Route::post('/generate-code', [TelegramController::class, 'generateLinkCode'])->name('generate-code');
    Route::post('/disconnect', [TelegramController::class, 'disconnect'])->name('disconnect');
    Route::get('/status', [TelegramController::class, 'status'])->name('status');
    Route::post('/test', [TelegramController::class, 'testNotification'])->name('test');
});

// Admin Routes (requires authentication + admin role)
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Kelola Peminjaman Routes
    Route::get('/peminjaman', function () {
        return view('admin.peminjaman.index');
    })->name('peminjaman');
    
    // Kelola Pengembalian Routes
    Route::get('/pengembalian', function () {
        return view('admin.pengembalian.index');
    })->name('pengembalian');
    
    // Data Alat Routes
    Route::get('/alat', function () {
        return view('admin.alat.index');
    })->name('alat');
    
    // Data Mahasiswa Routes
    Route::get('/mahasiswa', function () {
        return view('admin.mahasiswa.index');
    })->name('mahasiswa');
    
    // Data Dosen Routes
    Route::get('/dosen', function () {
        return view('admin.dosen.index');
    })->name('dosen');
    
    // Laporan Routes
    Route::get('/laporan', function () {
        return view('admin.laporan.index');
    })->name('laporan');
    
    // Profil Admin Routes
    Route::get('/profil', function () {
        return view('admin.profil');
    })->name('profil');

    // Kelola User Routes (Tambah Dosen, KA Lab, dll)
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

// Mahasiswa Routes (requires authentication + mahasiswa role)
Route::prefix('mahasiswa')->middleware(['auth', 'role:mahasiswa'])->name('mahasiswa.')->group(function () {
    Route::get('/dashboard', [MahasiswaDashboardController::class, 'index'])->name('dashboard');
    
    // Peminjaman Routes
    Route::get('/peminjaman/ajukan', function () {
        return view('mahasiswa.peminjaman.ajukan');
    })->name('peminjaman.ajukan');
    
    Route::get('/peminjaman/riwayat', function () {
        return view('mahasiswa.peminjaman.riwayat');
    })->name('peminjaman.riwayat');
    
    // Alat Routes
    Route::get('/alat', function () {
        return view('mahasiswa.alat.index');
    })->name('alat');
    
    // Profil Routes
    Route::get('/profil', function () {
        return view('mahasiswa.profil');
    })->name('profil');
});

// KA Lab Routes (requires authentication + kalab role)
Route::prefix('kalab')->middleware(['auth', 'role:kalab'])->name('kalab.')->group(function () {
    Route::get('/dashboard', [KalabDashboardController::class, 'index'])->name('dashboard');
    
    // Persetujuan Peminjaman
    Route::get('/persetujuan', function () {
        return view('kalab.persetujuan.index');
    })->name('persetujuan');
    
    // Data Alat
    Route::get('/alat', function () {
        return view('kalab.alat.index');
    })->name('alat');
    
    // Riwayat Peminjaman
    Route::get('/riwayat', function () {
        return view('kalab.riwayat.index');
    })->name('riwayat');
    
    // Laporan
    Route::get('/laporan', function () {
        return view('kalab.laporan.index');
    })->name('laporan');
    
    // Profil
    Route::get('/profil', function () {
        return view('kalab.profil');
    })->name('profil');
});

// Dosen Routes (requires authentication + dosen role)
Route::prefix('dosen')->middleware(['auth', 'role:dosen'])->name('dosen.')->group(function () {
    Route::get('/dashboard', [DosenDashboardController::class, 'index'])->name('dashboard');
    
    // Peminjaman Mahasiswa
    Route::get('/peminjaman', function () {
        return view('dosen.peminjaman.index');
    })->name('peminjaman');
    
    // Daftar Alat
    Route::get('/alat', function () {
        return view('dosen.alat.index');
    })->name('alat');
    
    // Riwayat
    Route::get('/riwayat', function () {
        return view('dosen.riwayat.index');
    })->name('riwayat');
    
    // Profil
    Route::get('/profil', function () {
        return view('dosen.profil');
    })->name('profil');
});
