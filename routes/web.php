<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MagangController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\DivisiController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// RUTE KONFIRMASI PUBLIK
Route::get('/konfirmasi/{pendaftaran}', [PendaftaranController::class, 'konfirmasiKehadiran'])
    ->name('daftar.konfirmasi');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- GRUP RUTE LOWONGAN MAGANG (KATALOG UNTUK SEMUA USER AUTH) ---
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan.index');

    // --- GRUP RUTE PENDAFTARAN (UNTUK SEMUA USER) ---
    Route::get('/pendaftaran-magang/create', [PendaftaranController::class, 'create'])->name('daftar.create');
    Route::get('/pendaftaran-magang', [PendaftaranController::class, 'index'])->name('daftar.index');
    Route::post('/pendaftaran-magang', [PendaftaranController::class, 'store'])->name('daftar.store');
    Route::get('/pendaftaran-magang/{pendaftaran}/edit', [PendaftaranController::class, 'edit'])->name('daftar.edit');
    Route::put('/pendaftaran-magang/{pendaftaran}', [PendaftaranController::class, 'update'])->name('daftar.update');
    Route::get('/pendaftaran-magang/{pendaftaran}', [PendaftaranController::class, 'show'])->name('daftar.show');
    Route::delete('/pendaftaran-magang/{pendaftaran}', [PendaftaranController::class, 'destroy'])->name('daftar.destroy');

    // --- GRUP RUTE DATA MAGANG ---
    Route::get('/magang', [MagangController::class, 'index'])->name('magang.index');
    Route::get('/magang/{magang}/edit', [MagangController::class, 'edit'])->name('magang.edit');
    Route::put('/magang/{magang}', [MagangController::class, 'update'])->name('magang.update');

    // --- GRUP KHUSUS ADMIN ---
    Route::middleware('admin')->group(function () {
        // Rute Admin untuk Lowongan Magang
        Route::get('/lowongan/create', [LowonganController::class, 'create'])->name('lowongan.create');
        Route::post('/lowongan', [LowonganController::class, 'store'])->name('lowongan.store');
        Route::get('/lowongan/{lowongan}/edit', [LowonganController::class, 'edit'])->name('lowongan.edit');
        Route::put('/lowongan/{lowongan}', [LowonganController::class, 'update'])->name('lowongan.update');
        Route::delete('/lowongan/{lowongan}', [LowonganController::class, 'destroy'])->name('lowongan.destroy');

        // Rute Admin untuk Magang
        Route::get('/magang/create', [MagangController::class, 'create'])->name('magang.create');
        Route::post('/magang', [MagangController::class, 'store'])->name('magang.store');
        Route::patch('/magang/{magang}/toggle-visibility', [MagangController::class, 'toggleVisibility'])->name('magang.toggleVisibility');
        Route::delete('/magang/{magang}', [MagangController::class, 'destroy'])->name('magang.destroy');

        // RUTE TINDAKAN ADMIN (Pendaftaran)
        Route::post('/pendaftaran-magang/{pendaftaran}/status', [PendaftaranController::class, 'updateStatus'])->name('daftar.updateStatus');
        Route::get('/pendaftaran-magang/{pendaftaran}/download/{field}', [PendaftaranController::class, 'downloadFile'])->name('daftar.downloadFile');

        // Rute Pengaturan Durasi
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Rute Manajemen Divisi
        Route::get('/divisi', [DivisiController::class, 'index'])->name('divisi.index');
        Route::post('/divisi', [DivisiController::class, 'store'])->name('divisi.store');
        Route::post('/divisi/update', [DivisiController::class, 'update'])->name('divisi.update');
        Route::post('/divisi/destroy', [DivisiController::class, 'destroy'])->name('divisi.destroy');
        Route::post('/divisi/reset', [DivisiController::class, 'reset'])->name('divisi.reset');
    });

    // Detail Lowongan Magang (bisa dilihat semua user auth)
    Route::get('/lowongan/{lowongan}', [LowonganController::class, 'show'])->name('lowongan.show');

    // Rute 'show' untuk Magang (semua user auth bisa lihat)
    Route::get('/magang/{magang}', [MagangController::class, 'show'])->name('magang.show');

    // --- CHATBOT (MagBot) ---
    Route::post('/chatbot', [\App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.message');
});

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
});

require __DIR__ . '/auth.php';
