<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [ScraperController::class, 'home']);
Route::get('/load-data', [ScraperController::class, 'loadData']);
Route::get('/get-word', [ScraperController::class, 'getWordFrequencyData']);

Route::get('/scrapper', [ScraperController::class, 'index']);

// Route::get('/admin', 'AdminController@index')->name('admin.login');
// Route::get('/google', 'AdminController@registerWithGoogle')->name('admin.registerWithGoogle');
// Route::get('/callback', 'AdminController@createUser');
// Route::get('/admin/pages/dashboard', 'AdminController@dashboard')->name('admin.dashboard');

Route::get('/register', [AdminController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AdminController::class, 'register']);
Route::get('/login', [AdminController::class, 'index'])->name('login');
Route::post('/login', [AdminController::class, 'login']);
Route::get('/logout', [AdminController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Tambahkan rute untuk halaman yang memerlukan autentikasi di sini.
    Route::get('/admin', [AdminController::class, 'dashboard']);

});