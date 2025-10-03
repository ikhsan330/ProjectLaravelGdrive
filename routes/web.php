
<?php

use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [WelcomeController::class, 'index'])
    ->name('welcome');
Route::get('/folder-contents/{folder}', [WelcomeController::class, 'getFolderContents'])
    ->name('folders.contents');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->role == 'admin') {
            return view('admin.dashboard');
        } elseif (Auth::user()->role == 'dosen') {
            return view('dosen.dashboard');
        } elseif (Auth::user()->role == 'kaprodi') {
            return view('kaprodi.dashboard');
        } elseif (Auth::user()->role == 'asesor') {
            return view('asesor.dashboard');
        } else{
            return view('p4pm.dashboard');
        }
    });
});

require __DIR__ . '/dosen-route.php';
require __DIR__ . '/admin-route.php';
require __DIR__ . '/kaprodi-route.php';
require __DIR__ . '/asesor-route.php';
require __DIR__ . '/p4pm-route.php';
require __DIR__ . '/auth.php';
