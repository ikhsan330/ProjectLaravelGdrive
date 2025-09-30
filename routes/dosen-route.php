<?php

use App\Http\Controllers\DosenController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenFolderController;
use App\Http\Controllers\DosenDocumentController;

Route::middleware(['auth', 'role:dosen'])->prefix('dosen')->group(function () {
   Route::get('/dashboard',[DosenController::class,'dashboard'])->name('dosen.dashboard');

    // Route untuk Folder
    Route::get('/dokumen', [DosenFolderController::class, 'index'])->name('dosen.dokumen.index');
    Route::get('/dokumen/{folder_id}', [DosenFolderController::class, 'show'])->name('dosen.folder.show');
    Route::post('/dokumen/subfolder', [DosenFolderController::class, 'storeSubfolder'])->name('dosen.folder.store-subfolder');
    Route::put('/dokumen/{id}', [DosenFolderController::class, 'update'])->name('dosen.folder.update');
    Route::delete('/dokumen/subfolder/{id}', [DosenFolderController::class, 'destroySubfolder'])->name('dosen.folder.destroy');

    // Anda mungkin perlu membuat DosenDocumentController atau menyesuaikan yang ada.
    Route::post('/dokumen', [DosenDocumentController::class, 'store'])->name('dosen.document.store');
    Route::get('/dokumen/{id}/show', [DosenDocumentController::class, 'show'])->name('dosen.document.show');
    Route::get('/dokumen/{id}/download', [DosenDocumentController::class, 'download'])->name('dosen.document.download');
    Route::put('/dokumen/{id}/update', [DosenDocumentController::class, 'update'])->name('dosen.document.update');
    Route::delete('/dokumen/{id}/destroy', [DosenDocumentController::class, 'destroy'])->name('dosen.document.destroy');
});


