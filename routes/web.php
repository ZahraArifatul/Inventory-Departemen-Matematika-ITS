<?php

use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\PastikanAkunAktif;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// ---------- Login ----------
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.proses');

// ---------- Semua pengguna yang sudah login ----------
Route::middleware(['auth', PastikanAkunAktif::class])->group(function () {

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    // Admin & Teknisi: input / ubah data operasional (didefinisikan dulu agar /barang/create tidak tertukar dengan /barang/{id})
    Route::middleware(RoleMiddleware::class . ':admin,teknisi')->group(function () {
        Route::get('barang/create', [BarangController::class, 'create'])->name('barang.create');
        Route::post('barang', [BarangController::class, 'store'])->name('barang.store');
        Route::get('barang/{barang}/edit', [BarangController::class, 'edit'])->name('barang.edit');
        Route::put('barang/{barang}', [BarangController::class, 'update'])->name('barang.update');
        Route::patch('barang/{barang}/kondisi', [BarangController::class, 'ubahKondisi'])->name('barang.kondisi');

        Route::post('barang/{barang}/mutasi', [MutasiController::class, 'store'])->name('barang.mutasi.store');

        Route::post('barang/{barang}/perbaikan', [PerbaikanController::class, 'store'])->name('barang.perbaikan.store');
        Route::get('perbaikan/{perbaikan}/edit', [PerbaikanController::class, 'edit'])->name('perbaikan.edit');
        Route::put('perbaikan/{perbaikan}', [PerbaikanController::class, 'update'])->name('perbaikan.update');
    });

    // Admin & Manajemen: laporan / ekspor
    Route::middleware(RoleMiddleware::class . ':admin,manajemen')->group(function () {
        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export/{jenis}', [LaporanController::class, 'export'])
            ->where('jenis', 'barang|perbaikan|mutasi|analisis')->name('laporan.export');
    });

    // Admin saja: data master & pengguna
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::resource('ruangan', RuanganController::class)->only(['index', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['ruangan' => 'ruangan']);
        Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['kategori' => 'kategori']);
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Semua peran: lihat / monitoring
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('barang', [BarangController::class, 'index'])->name('barang.index');
    Route::get('barang/{barang}', [BarangController::class, 'show'])->name('barang.show');
    Route::get('mutasi', [MutasiController::class, 'index'])->name('mutasi.index');
    Route::get('perbaikan', [PerbaikanController::class, 'index'])->name('perbaikan.index');
    Route::get('analisis', [AnalisisController::class, 'index'])->name('analisis.index');
});
