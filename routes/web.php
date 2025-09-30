
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     if (Auth::user()->role === 'admin') {
//         return view('admin.dashboard');
//     } elseif (Auth::user()->role === 'dosen') {
//         return view('dosen.dashboard');
//     } else {
//         return view('kaprodi.dashboard');
//     }
// })->middleware(['auth', 'verified']);

require __DIR__ . '/dosen-route.php';
require __DIR__ . '/admin-route.php';
require __DIR__ . '/kaprodi-route.php';
require __DIR__ . '/auth.php';
