<?php

use App\Http\Controllers\MahasiswaLaporanController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PerbaikanController;
use App\Http\Controllers\LabController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PenugasanController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIK - MAHASISWA (Tidak perlu login)
|--------------------------------------------------------------------------
*/

Route::get('/', [MahasiswaLaporanController::class, 'index'])->name('mahasiswa.form');
Route::post('/laporan', [MahasiswaLaporanController::class, 'store'])->name('mahasiswa.store');
Route::get('/status', [MahasiswaLaporanController::class, 'status'])->name('mahasiswa.status');
Route::get('/status/{no_laporan}', [MahasiswaLaporanController::class, 'showStatus'])
    ->name('mahasiswa.status.show')
    ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');

/*
|--------------------------------------------------------------------------
| REDIRECT HELPER
|--------------------------------------------------------------------------
*/

Route::get('/login', fn () => redirect('/dashboard/login'))->name('login');

/*
|--------------------------------------------------------------------------
| INTERNAL - Wajib Login
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('internal')->name('internal.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | PDF & EXPORT
    | Hanya bisa diakses oleh SPV (semua jenis SPV)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan'])
        ->prefix('pdf')
        ->name('pdf.')
        ->group(function () {

            // Cetak riwayat perbaikan semua/per-lab dengan filter tanggal
            // Contoh: /internal/pdf/riwayat-perbaikan?id_lab=1&dari=2024-01-01&sampai=2024-12-31
            Route::get('/riwayat-perbaikan', [PdfController::class, 'cetakRiwayat'])
                ->name('riwayat');

            // Cetak rekap per-lab
            // Contoh: /internal/pdf/rekap-lab/1?dari=2024-01-01&sampai=2024-12-31
            Route::get('/rekap-lab/{id_lab}', [PdfController::class, 'cetakRekapLab'])
                ->name('rekap.lab');
        });

    // Cetak detail satu laporan — SPV + Admin Lab
    Route::get('/pdf/laporan/{no_laporan}', [PdfController::class, 'cetakDetailLaporan'])
        ->name('pdf.laporan.detail')
        ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}')
        ->middleware('role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan,admin_lab');

    /*
    |----------------------------------------------------------------------
    | LAPORAN — Validasi oleh SPV/PIC
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan'])
        ->prefix('laporan')
        ->name('laporan.')
        ->group(function () {

            // Setujui laporan + delegasi ke admin
            Route::patch('/{no_laporan}/setujui', [LaporanController::class, 'setujui'])
                ->name('setujui')
                ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');

            // Tolak laporan dengan alasan
            Route::patch('/{no_laporan}/tolak', [LaporanController::class, 'tolak'])
                ->name('tolak')
                ->where('no_laporan', 'LPR-[0-9]{8}-[0-9]{4}');
        });

    /*
    |----------------------------------------------------------------------
    | PERBAIKAN — Admin Lab & SPV
    |----------------------------------------------------------------------
    */
    Route::prefix('perbaikan')->name('perbaikan.')->group(function () {

        // Update status perbaikan — hanya Admin Lab
        Route::patch('/{id_perbaikan}/status', [PerbaikanController::class, 'updateStatus'])
            ->name('update.status')
            ->middleware('role:admin_lab');

        // Upload foto bukti — hanya Admin Lab
        Route::post('/{id_perbaikan}/upload-bukti', [PerbaikanController::class, 'uploadBukti'])
            ->name('upload.bukti')
            ->middleware('role:admin_lab');

        // Validasi perbaikan selesai — hanya SPV
        Route::patch('/{id_perbaikan}/validasi', [PerbaikanController::class, 'validasi'])
            ->name('validasi')
            ->middleware('role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan');

        // Kembalikan perbaikan ke admin — hanya SPV
        Route::patch('/{id_perbaikan}/kembalikan', [PerbaikanController::class, 'kembalikan'])
            ->name('kembalikan')
            ->middleware('role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan');
    });

    /*
    |----------------------------------------------------------------------
    | MANAJEMEN LAB — Hanya SPV Kedisiplinan
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan'])
        ->prefix('lab')
        ->name('lab.')
        ->group(function () {

            Route::post('/', [LabController::class, 'store'])->name('store');
            Route::patch('/{id_lab}', [LabController::class, 'update'])->name('update');
            Route::patch('/{id_lab}/nonaktif', [LabController::class, 'nonaktif'])->name('nonaktif');
        });

    /*
    |----------------------------------------------------------------------
    | MANAJEMEN USER — Hanya SPV Kedisiplinan
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan'])
        ->prefix('user')
        ->name('user.')
        ->group(function () {

            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::patch('/{id_user}', [UserController::class, 'update'])->name('update');
            Route::patch('/{id_user}/nonaktif', [UserController::class, 'nonaktif'])->name('nonaktif');
            Route::patch('/{id_user}/reset-password', [UserController::class, 'resetPassword'])->name('reset.password');
        });

    /*
    |----------------------------------------------------------------------
    | PENUGASAN USER ke LAB — Hanya SPV Kedisiplinan
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan'])
        ->prefix('penugasan')
        ->name('penugasan.')
        ->group(function () {

            Route::post('/', [PenugasanController::class, 'store'])->name('store');
            Route::patch('/{id_penugasan}', [PenugasanController::class, 'update'])->name('update');
            Route::patch('/{id_penugasan}/nonaktif', [PenugasanController::class, 'nonaktif'])->name('nonaktif');
        });

    /*
    |----------------------------------------------------------------------
    | MONITORING — Hanya SPV (semua jenis)
    |----------------------------------------------------------------------
    */
    Route::middleware(['role:spv_kedisiplinan,spv_jaringan,spv_inovasi_riset,spv_penjadwalan,spv_inventory,spv_keuangan'])
        ->prefix('monitoring')
        ->name('monitoring.')
        ->group(function () {

            // Data statistik ringkasan untuk dashboard
            Route::get('/statistik', [MonitoringController::class, 'statistik'])->name('statistik');

            // Data grafik perbaikan per bulan
            Route::get('/grafik-bulanan', [MonitoringController::class, 'grafikBulanan'])->name('grafik.bulanan');

            // Data rekap perbaikan per lab
            Route::get('/per-lab', [MonitoringController::class, 'perLab'])->name('per.lab');
        });
});