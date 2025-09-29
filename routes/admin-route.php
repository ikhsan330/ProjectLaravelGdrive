<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminFolderController;
use App\Http\Controllers\AdminUserController;

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/create', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');


    Route::get('/admin/dokumen/index', [AdminFolderController::class, 'createFolderForm'])->name('admin.dokumen.index');
    Route::post('/admin/folder/store', [AdminFolderController::class, 'createFolderStructure'])->name('admin.folder.store');
    Route::get('/admin/dokumen/{dosen_id}/folder/{folder_id}', [AdminFolderController::class, 'showDosenFolder'])->name('admin.dosen.folder.show');
    Route::put('/admin/dokumen/folder/{id}', [AdminFolderController::class, 'update'])->name('admin.folder.update');
    Route::delete('/admin/dokumen/folder/{id}', [AdminFolderController::class, 'destroy'])->name('admin.folder.destroy');
    Route::post('/dokumen/folder/subfolder', [AdminFolderController::class, 'storeSubfolderStructure'])->name('admin.folder.store-subfolder');
    Route::post('/dokumen/folder/reassign', [AdminFolderController::class, 'reassignFolder'])->name('admin.folder.reassign');
    Route::get('/dokumen/folder/{folderId}/details', [AdminFolderController::class, 'getFolderDetails'])->name('admin.folder.details');
    Route::delete('/admin/folder/destroy-master/{folder_id}', [AdminFolderController::class, 'destroyMasterFolder'])->name('admin.folder.destroy.master');
    // Menghapus sub-folder secara permanen dari Google Drive
    Route::delete('/subfolder/{id}', [AdminFolderController::class, 'destroySubfolder'])->name('admin.subfolder.destroy');
});

