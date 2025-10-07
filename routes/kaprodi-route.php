<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\KaprodiDocumentController;

// Gunakan prefix 'kaprodi' untuk membuat URL lebih rapi
Route::middleware(['auth','role:kaprodi'])->prefix('kaprodi')->group(function () {

    // [OK] Route untuk Dashboard Kaprodi
    Route::get('/dashboard', [KaprodiController::class, 'dashboard'])->name('kaprodi.dashboard');

    // =================================================================
    // == ROUTE UNTUK MENAMPILKAN FOLDER & DOKUMEN
    // =================================================================

    Route::get('/dokumen', [KaprodiDocumentController::class, 'index'])->name('kaprodi.dokumen.index');

    Route::get('/folder/{folder_id}', [KaprodiDocumentController::class, 'show'])->name('kaprodi.folder.show');

    Route::get('/dokumen/unverified', [KaprodiDocumentController::class, 'showUnverified'])->name('kaprodi.dokumen.unverified');

    // =================================================================
    // == ROUTE UNTUK AKSI PADA DOKUMEN SPESIFIK
    // =================================================================

    Route::patch('/document/{id}/verify', [KaprodiDocumentController::class, 'updateVerification'])->name('kaprodi.document.verify');

    Route::get('/document/{id}/preview', [KaprodiDocumentController::class, 'previewDocument'])->name('kaprodi.document.preview');

    Route::get('/document/{id}/download', [KaprodiDocumentController::class, 'downloadDocument'])->name('kaprodi.document.download');

});
