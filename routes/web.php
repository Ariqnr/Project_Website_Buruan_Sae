<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SampahController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'role:'.User::ROLE_ADMIN.','.User::ROLE_PENGELOLA])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('sampah', SampahController::class)->except(['show']);

    /*
     * Halaman sementara. Akan diganti pada tahap berikutnya:
     * Jadwal Penjemuran (tahap 5), Laporan (tahap 6).
     */
    Route::view('/jadwal-penjemuran', 'segera-hadir', ['judul' => 'Jadwal Penjemuran'])->name('jadwal.index');
    Route::view('/laporan', 'segera-hadir', ['judul' => 'Laporan'])->name('laporan.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
