<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Music\MusicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\ScorePartController;
use App\Http\Controllers\Admin\ConcertController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Music (auth + musician/admin)
Route::middleware(['auth', 'musician'])->group(function () {
    Route::get('/muziek', [MusicController::class, 'index'])->name('muziek.index');
    Route::get('/muziek/bladmuziek/{score}/partijen/{part}/download', [MusicController::class, 'download'])->name('muziek.download');
});

// Admin (Admin Panel) - URLs stay Dutch for users
Route::middleware(['auth', 'admin'])->prefix('beheer')->name('beheer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('bladmuziek', ScoreController::class);
    Route::post('bladmuziek/{score}/partijen', [ScorePartController::class, 'store'])->name('bladmuziek.partijen.store');
    Route::delete('partijen/{part}', [ScorePartController::class, 'destroy'])->name('partijen.destroy');
    Route::resource('concerten', ConcertController::class);
    Route::resource('gebruikers', UserController::class)->only(['index', 'create', 'store']);
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
