<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\KategoriController;

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Dokumen / Arsip
    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('/', [DokumenController::class, 'index'])->name('index');
        Route::get('/create', [DokumenController::class, 'create'])->name('create')->middleware('can:upload-dokumen');
        Route::post('/', [DokumenController::class, 'store'])->name('store')->middleware('can:upload-dokumen');
        Route::get('/{dokumen}', [DokumenController::class, 'show'])->name('show');
        Route::get('/{dokumen}/edit', [DokumenController::class, 'edit'])->name('edit')->middleware('can:edit-dokumen');
        Route::put('/{dokumen}', [DokumenController::class, 'update'])->name('update')->middleware('can:edit-dokumen');
        Route::delete('/{dokumen}', [DokumenController::class, 'destroy'])->name('destroy')->middleware('can:delete-dokumen');
        Route::get('/{dokumen}/download', [DokumenController::class, 'download'])->name('download');
        Route::get('/{dokumen}/preview', [DokumenController::class, 'preview'])->name('preview');
        Route::patch('/{dokumen}/status', [DokumenController::class, 'updateStatus'])->name('update-status')->middleware('can:approve-dokumen');
    });

    // Buku Agenda
    Route::prefix('agenda')->name('agenda.')->group(function () {
        Route::get('/', [AgendaController::class, 'index'])->name('index');
        Route::get('/create', [AgendaController::class, 'create'])->name('create')->middleware('can:upload-dokumen');
        Route::post('/', [AgendaController::class, 'store'])->name('store')->middleware('can:upload-dokumen');
        Route::get('/{agenda}', [AgendaController::class, 'show'])->name('show');
        Route::get('/{agenda}/edit', [AgendaController::class, 'edit'])->name('edit')->middleware('can:edit-dokumen');
        Route::put('/{agenda}', [AgendaController::class, 'update'])->name('update')->middleware('can:edit-dokumen');
        Route::delete('/{agenda}', [AgendaController::class, 'destroy'])->name('destroy')->middleware('can:delete-dokumen');
    });

    // Laporan & Ekspor
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/export-excel', [LaporanController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [LaporanController::class, 'exportPdf'])->name('export-pdf');
    });

    // Log Aktivitas
    Route::get('/log-aktivitas', [ActivityLogController::class, 'index'])->name('log.index')->middleware('can:view-logs');

    // Manajemen Pengguna (Super Admin only)
    Route::prefix('pengguna')->name('pengguna.')->middleware('can:manage-users')->group(function () {
        Route::get('/', [PenggunaController::class, 'index'])->name('index');
        Route::get('/create', [PenggunaController::class, 'create'])->name('create');
        Route::post('/', [PenggunaController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [PenggunaController::class, 'edit'])->name('edit');
        Route::put('/{user}', [PenggunaController::class, 'update'])->name('update');
        Route::delete('/{user}', [PenggunaController::class, 'destroy'])->name('destroy');
        Route::patch('/{user}/toggle-active', [PenggunaController::class, 'toggleActive'])->name('toggle-active');
    });

    // Kategori
    Route::resource('kategori', KategoriController::class)->middleware('can:manage-users');

    // Backup & Restore (Super Admin only)
    Route::prefix('backup')->name('backup.')->middleware('can:manage-backup')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::get('/{filename}/download', [BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
        Route::post('/restore', [BackupController::class, 'restore'])->name('restore');
    });
});