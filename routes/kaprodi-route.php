<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\KaprodiDocumentController;



Route::middleware(['auth','role:kaprodi'])->group(function () {
    Route::get('/kaprodi/dashboard', [KaprodiController::class, 'dashboard'])->name('kaprodi.dashboard');

     Route::get('/kaprodi/index', [KaprodiDocumentController::class, 'index'])
         ->name('kaprodi.dokumen.index');

    // Menampilkan isi folder spesifik dari seorang dosen
    Route::get('/show/{dosen_id}/{folder_id}', [KaprodiDocumentController::class, 'showDosenFolder'])
         ->name('kaprodi.dokumen.show');

    // Memproses pembaruan status verifikasi dokumen
    Route::patch('/verify/{id}', [KaprodiDocumentController::class, 'updateVerification'])
         ->name('kaprodi.dokumen.verify');

    // Mengarahkan ke pratinjau dokumen di Google Drive
    Route::get('/preview/{id}', [KaprodiDocumentController::class, 'previewDocument'])
         ->name('kaprodi.dokumen.preview');

    // Mengunduh dokumen dari Google Drive
    Route::get('/download/{id}', [KaprodiDocumentController::class, 'downloadDocument'])
         ->name('kaprodi.dokumen.download');

    Route::get('/unverified', [KaprodiDocumentController::class, 'showUnverified'])
         ->name('kaprodi.dokumen.unverified');

});

