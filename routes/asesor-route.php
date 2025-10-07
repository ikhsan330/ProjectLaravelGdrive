<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsesorController;
use App\Http\Controllers\AssesorDocumentController;

Route::middleware(['auth', 'role:asesor'])->prefix('asesor')->group(function (){

    Route::get('/dashboard', [AsesorController::class, 'dashboard'])->name('asesor.dashboard');

        // Halaman utama: Menampilkan daftar folder induk publik
    Route::get('/dokumen', [AssesorDocumentController::class, 'index'])
         ->name('assesor.dokumen.index');

    // Menampilkan isi dari sebuah folder (sub-folder dan dokumen terverifikasi)
    Route::get('/folder/{folder_id}', [AssesorDocumentController::class, 'show'])
         ->name('assesor.folder.show');

    // Menyimpan komentar baru pada sebuah dokumen
    Route::post('/document/{document_id}/comments', [AssesorDocumentController::class, 'storeComment'])
         ->name('assesor.document.comment.store');

    Route::get('/document/{id}/preview', [AssesorDocumentController::class, 'previewDocument'])
         ->name('assesor.document.preview');
});
