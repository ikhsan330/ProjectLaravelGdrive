<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\P4pmController;
Route::middleware(['auth', 'role:p4pm'])->prefix('p4pm')->group(function () {
    Route::get('/dashboard', [P4pmController::class, 'dashboard'])->name('p4pm.dashboard');
});
