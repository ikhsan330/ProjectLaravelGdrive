<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\P4pmController;
use App\Http\Controllers\P4pmDocumentController;


Route::middleware(['auth', 'role:p4pm'])->prefix('p4pm')->group(function () {

    Route::get('/dashboard', [P4pmController::class, 'dashboard'])->name('p4pm.dashboard');
      // Halaman utama: Menampilkan daftar folder induk publik
    Route::get('/dokumen', [P4pmDocumentController::class, 'index'])
         ->name('p4pm.dokumen.index');

    // Menampilkan isi dari sebuah folder (sub-folder dan dokumen terverifikasi)
    Route::get('/folder/{folder_id}', [P4pmDocumentController::class, 'show'])
         ->name('p4pm.folder.show');

    // Menyimpan komentar baru pada sebuah dokumen
    Route::post('/document/{document_id}/comments', [P4pmDocumentController::class, 'storeComment'])
         ->name('p4pm.document.comment.store');

    // Melihat pratinjau dokumen
    Route::get('/document/{id}/preview', [P4pmDocumentController::class, 'previewDocument'])
         ->name('p4pm.document.preview');

});
