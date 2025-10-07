<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDocumentController;
use App\Http\Controllers\AdminFolderController;
use App\Http\Controllers\AdminUserController;
    use App\Http\Controllers\SettingController;


// =================================================================
// == GRUP ROUTE ADMIN
// =================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // == Dashboard ==
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // == Manajemen User == (Tidak ada perubahan)
    Route::resource('users', AdminUserController::class)->except(['show'])->names([
        'index' => 'admin.users.index',
        'create' => 'admin.users.create',
        'store' => 'admin.users.store',
        'edit' => 'admin.users.edit',
        'update' => 'admin.users.update',
        'destroy' => 'admin.users.destroy',
    ]);


    // =================================================================
    // == MANAJEMEN FOLDER PUBLIK
    // =================================================================

    // [OK] Halaman utama manajemen folder (menampilkan folder induk)
    Route::get('/dokumen', [AdminFolderController::class, 'index'])->name('admin.dokumen.index');

    // [OK] Menampilkan isi sebuah folder (sub-folder dan dokumen di dalamnya)
    Route::get('/folder/{folder_id}', [AdminFolderController::class, 'showFolder'])->name('admin.folder.show');

    // [DIPERBAIKI] Membuat folder induk baru. Namanya disamakan dengan view.
    Route::post('/folder/create', [AdminFolderController::class, 'createFolderStructure'])->name('admin.folder.create.structure');

    // [OK] Update nama folder.
    Route::put('/folder/{id}', [AdminFolderController::class, 'update'])->name('admin.folder.update');

    // [OK] Menghapus folder (bisa induk atau sub-folder).
    Route::delete('/folder/{folder_id}', [AdminFolderController::class, 'destroy'])->name('admin.folder.destroy');

    Route::post('/folder/store-subfolder', [AdminFolderController::class, 'storeSubfolderStructure'])
        ->name('admin.folder.store-subfolder');
    // =================================================================
    // == MANAJEMEN DOKUMEN
    // =================================================================

    // [OK] Menyimpan dokumen yang baru di-upload.
    Route::post('/dokumen', [AdminDocumentController::class, 'store'])->name('admin.dokumen.store');

    // [OK] Pratinjau dokumen di tab baru.
    // Anda sudah benar mengganti URL-nya menjadi /preview untuk menghindari konflik.
    Route::get('/dokumen/{id}/preview', [AdminDocumentController::class, 'show'])->name('admin.dokumen.show');

    // [OK] Mengunduh dokumen.
    Route::get('/dokumen/{id}/download', [AdminDocumentController::class, 'download'])->name('admin.dokumen.download');

    // [OK] Update detail dokumen.
    Route::put('/dokumen/{id}', [AdminDocumentController::class, 'update'])->name('admin.dokumen.update');

    // [OK] Menghapus dokumen.
    Route::delete('/dokumen/{id}', [AdminDocumentController::class, 'destroy'])->name('admin.dokumen.destroy');

    Route::get('/dokumen/commented', [AdminDocumentController::class, 'showCommented'])->name('admin.dokumen.commented');


Route::get('/settings/google-drive', [SettingController::class, 'edit'])->name('admin.settings.google.edit');
Route::post('/settings/google-drive', [SettingController::class, 'update'])->name('admin.settings.google.update');
});
