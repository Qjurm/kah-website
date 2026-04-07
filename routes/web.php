<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Muziek\MuziekController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Beheer\DashboardController;
use App\Http\Controllers\Beheer\ScoreController;
use App\Http\Controllers\Beheer\ScorePartController;
use App\Http\Controllers\Beheer\ConcertController;
use App\Http\Controllers\Beheer\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Muziek (auth + musician/admin)
Route::middleware(['auth', 'musician'])->group(function () {
    Route::get('/muziek', [MuziekController::class, 'index'])->name('muziek.index');
    Route::get('/muziek/bladmuziek/{score}/partijen/{part}/download', [MuziekController::class, 'download'])->name('muziek.download');
});

// Beheer (Admin Panel)
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
