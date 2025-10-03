<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsesorController;

Route::middleware(['auth', 'role:asesor'])->prefix('asesor')->group(function (){
    Route::get('/dashboard', [AsesorController::class, 'dashboard'])->name('asesor.dashboard');
});
