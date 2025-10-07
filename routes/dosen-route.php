<?php

use App\Http\Controllers\DosenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenFolderController;
use App\Http\Controllers\DosenDocumentController;

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {

    Route::get('/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');

    // =================================================================
    // == ROUTE FOLDER (HANYA UNTUK MENAMPILKAN)
    // =================================================================

    Route::get('/dokumen', [DosenFolderController::class, 'index'])->name('dosen.dokumen.index');
    Route::get('/folder/{folder_id}', [DosenFolderController::class, 'show'])->name('dosen.folder.show');




    // =================================================================
    // == ROUTE DOKUMEN (SEMUA AKSI CRUD)
    // =================================================================

    // [OK] Menyimpan dokumen baru yang di-upload
    Route::post('/document', [DosenDocumentController::class, 'store'])->name('dosen.document.store');

    Route::get('/document/{id}/preview', [DosenDocumentController::class, 'show'])->name('dosen.document.show');

    Route::get('/document/{id}/download', [DosenDocumentController::class, 'download'])->name('dosen.document.download');

    Route::put('/document/{id}', [DosenDocumentController::class, 'update'])->name('dosen.document.update');

    Route::delete('/document/{id}', [DosenDocumentController::class, 'destroy'])->name('dosen.document.destroy');
});
