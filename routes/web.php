<?php

use App\Http\Controllers\MahasiswaLaporanController;
use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIK - MAHASISWA
|--------------------------------------------------------------------------
*/

// HALAMAN HOME
Route::get('/', [MahasiswaLaporanController::class, 'home'])
    ->name('mahasiswa.home');

// HALAMAN FORM LAPORAN
Route::get('/buat-laporan', [MahasiswaLaporanController::class, 'index'])
    ->name('mahasiswa.form');

// Submit form laporan
Route::post('/laporan', [MahasiswaLaporanController::class, 'store'])
    ->name('mahasiswa.store');

// Halaman cek status laporan
Route::get('/status', [MahasiswaLaporanController::class, 'status'])
    ->name('mahasiswa.status');

// Cek status satu laporan spesifik
Route::get('/status/{no_laporan}', [MahasiswaLaporanController::class, 'showStatus'])
    ->name('mahasiswa.status.show')
    ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');

/*
|--------------------------------------------------------------------------
| PDF - Wajib login, hanya SPV dan Admin Lab
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('internal')->name('internal.')->group(function () {

    // Cetak PDF riwayat perbaikan (semua/per-lab, filter tanggal)
    // Dipanggil dari Filament Page CetakPdf
    // Contoh: /internal/pdf/riwayat-perbaikan?id_lab=1&dari=2024-01-01&sampai=2024-12-31
    Route::get('/pdf/riwayat-perbaikan', [PdfController::class, 'cetakRiwayat'])
        ->name('pdf.riwayat-perbaikan');

    // Cetak PDF detail satu laporan
    Route::get('/pdf/laporan/{no_laporan}', [PdfController::class, 'cetakDetailLaporan'])
        ->name('pdf.laporan.detail')
        ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');

    // Cetak PDF rekap per-lab
    Route::get('/pdf/rekap-lab/{id_lab}', [PdfController::class, 'cetakRekapLab'])
        ->name('pdf.rekap.lab');
});

/*
|--------------------------------------------------------------------------
| REDIRECT HELPER
|--------------------------------------------------------------------------
*/

// Redirect /login ke halaman login Filament
Route::get('/login', fn () => redirect('/dashboard/login'))->name('login');