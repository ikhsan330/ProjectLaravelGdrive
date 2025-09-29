
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminFolderController;
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


require __DIR__ . '/dosen-route.php';
require __DIR__ . '/admin-route.php';

Route::middleware(['auth', 'role:kaprodi'])->group(function () {
    Route::get('/kaprodi/dashboard', [KaprodiController::class, 'dashboard'])->name('kaprodi.dashboard');
    Route::get('/kaprodi/dokumen/index', [KaprodiController::class, 'index'])->name('kaprodi.dokumen.index');
});


require __DIR__ . '/auth.php';
