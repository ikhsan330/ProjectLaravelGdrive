<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');

    Route::get('/dosen/dokumen/index', [DocumentController::class, 'index'])->name('dosen.dokumen.index');
    Route::get('/dosen/dokumen/create', [DocumentController::class, 'create'])->name('dosen.dokumen.create');
    Route::post('/dosen/dokumen/store', [DocumentController::class, 'store'])->name('dosen.dokumen.store');
    Route::get('/dosen/dokumen/{id}/show', [DocumentController::class, 'show'])->name('dosen.dokumen.show');
    Route::get('/dosen/dokumen/{id}/download', [DocumentController::class, 'download'])->name('dosen.dokumen.download');
    Route::delete('/dosen/dokumen/{id}/destroy', [DocumentController::class, 'destroy'])->name('dosen.dokumen.destroy');
    Route::put('/dosen/dokumen/{id}', [DocumentController::class, 'update'])->name('dosen.dokumen.update');

    Route::post('/dosen/folder/store', [FolderController::class, 'createFolderStructure'])->name('dosen.folder.store');
    Route::put('/dosen/folder/{id}/update', [FolderController::class, 'update'])->name('dosen.folder.update');
    Route::delete('/dosen/folder/{id}/destroy', [FolderController::class, 'destroy'])->name('dosen.folder.destroy');
});


