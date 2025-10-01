<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AdminFolderController;
use App\Http\Controllers\AdminUserController;

// Semua route admin dibungkus dalam middleware dan prefix group
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // == Dashboard ==
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // == Manajemen User == (Sudah Baik)
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store'); // Diubah dari '/users/create' agar lebih RESTful
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');

    // =================================================================
    // == Manajemen Folder & Dokumen
    // =================================================================

    // Halaman utama manajemen folder
    Route::get('/dokumen', [AdminFolderController::class, 'createFolderForm'])->name('admin.dokumen.index');

    // -- Aksi Terkait Folder Induk (Master Folder) --
    Route::post('/folder', [AdminFolderController::class, 'createFolderStructure'])->name('admin.folder.store');
    Route::post('/folder/reassign', [AdminFolderController::class, 'reassignFolder'])->name('admin.folder.reassign');
    Route::delete('/folder/master/{folder_id}', [AdminFolderController::class, 'destroyMasterFolder'])->name('admin.folder.destroy.master');

    // -- Aksi Terkait Folder Spesifik (Induk atau Sub-folder) --
    Route::get('/folder/{dosen_id}/{folder_id}', [AdminFolderController::class, 'showDosenFolder'])->name('admin.dosen.folder.show');
    Route::put('/folder/{id}', [AdminFolderController::class, 'update'])->name('admin.folder.update');
    Route::delete('/folder/{id}', [AdminFolderController::class, 'destroy'])->name('admin.folder.destroy'); // Ini untuk hapus penugasan

    // -- Aksi Terkait Sub-folder --
    Route::post('/subfolder', [AdminFolderController::class, 'storeSubfolderStructure'])->name('admin.folder.store-subfolder');
    Route::delete('/subfolder/{id}', [AdminFolderController::class, 'destroySubfolder'])->name('admin.subfolder.destroy');

    // -- Aksi Terkait Dokumen --
    Route::post('/dokumen', [AdminDocumentController::class, 'store'])->name('admin.dokumen.store');
    Route::get('/dokumen/{id}', [AdminDocumentController::class, 'show'])->name('admin.dokumen.show');
    Route::get('/dokumen/{id}/download', [AdminDocumentController::class, 'download'])->name('admin.dokumen.download');
    Route::put('/dokumen/{id}', [AdminDocumentController::class, 'update'])->name('admin.dokumen.update');
    Route::delete('/dokumen/{id}', [AdminDocumentController::class, 'destroy'])->name('admin.dokumen.destroy');
    // di dalam file routes/web.php
});
