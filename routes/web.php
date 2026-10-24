<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JadwalPenjemuranController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampahController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'role:'.User::ROLE_ADMIN.','.User::ROLE_PENGELOLA])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('sampah', SampahController::class)->except(['show']);
    Route::resource('jadwal', JadwalPenjemuranController::class)->except(['show']);
    Route::redirect('/jadwal-penjemuran', '/jadwal');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/unduh', [LaporanController::class, 'unduh'])->name('laporan.unduh');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Manajemen Pengguna & Log Aktivitas (Khusus Admin)
    Route::middleware('role:'.User::ROLE_ADMIN)->group(function () {
        Route::resource('pengguna', PenggunaController::class)->except(['show', 'destroy']);
        Route::patch('/pengguna/{pengguna}/toggle-status', [PenggunaController::class, 'toggleStatus'])
            ->name('pengguna.toggle-status');
        Route::post('/pengguna/{pengguna}/reset-password', [PenggunaController::class, 'resetPassword'])
            ->name('pengguna.reset-password');

        Route::get('/log', [ActivityLogController::class, 'index'])->name('log.index');
    });
});

require __DIR__.'/auth.php';
