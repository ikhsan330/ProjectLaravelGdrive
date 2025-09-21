
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FolderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/dokumen/index', [AdminController::class, 'index'])->name('admin.dokumen.index');
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users/create', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{user}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});

Route::middleware(['auth', 'role:dosen'])->group(function () {
    Route::get('/dosen/dashboard', [DosenController::class, 'dashboard'])->name('dosen.dashboard');

    Route::get('/dosen/dokumen/index', [DocumentController::class, 'index'])->name('dosen.dokumen.index');
    Route::get('/dosen/dokumen/create', [DocumentController::class, 'create'])->name('dosen.dokumen.create');
    Route::post('/dosen/dokumen/store', [DocumentController::class, 'store'])->name('dosen.dokumen.store');
    Route::get('/dosen/dokumen/{id}/show', [DocumentController::class, 'show'])->name('dosen.dokumen.show');
    Route::get('/dosen/dokumen/{id}/download', [DocumentController::class, 'download'])->name('dosen.dokumen.download');
    Route::delete('/dosen/dokumen/{id}/destroy', [DocumentController::class, 'destroy'])->name('dosen.dokumen.destroy');
    Route::put('/dosen/dokumen/{id}', [DocumentController::class, 'update'])->name('dosen.dokumen.update');


    // Rute untuk folder
    // Route::get('/folders/create', [FolderController::class, 'createFolderForm'])->name('folders.create');
    Route::post('/dosen/folder/store', [FolderController::class, 'createFolderStructure'])->name('dosen.folder.store');
    Route::put('/dosen/folder/{id}/update', [FolderController::class, 'update'])->name('dosen.folder.update');
    Route::delete('/dosen/folder/{id}/destroy', [FolderController::class, 'destroy'])->name('dosen.folder.destroy');
});

Route::middleware(['auth', 'role:kaprodi'])->group(function () {
    Route::get('/kaprodi/dashboard', [KaprodiController::class, 'dashboard'])->name('kaprodi.dashboard');
    Route::get('/kaprodi/dokumen/index', [KaprodiController::class, 'index'])->name('kaprodi.dokumen.index');
});

require __DIR__ . '/auth.php';
