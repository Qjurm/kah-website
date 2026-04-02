<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MuziekController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\ScorePartController;
use App\Http\Controllers\Admin\ConcertController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Muziek (auth + musician/admin)
Route::middleware(['auth', 'musician'])->group(function () {
    Route::get('/muziek', [MuziekController::class, 'index'])->name('muziek.index');
    Route::get('/muziek/scores/{score}/parts/{part}/download', [MuziekController::class, 'download'])->name('muziek.download');
});

// Admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('scores', ScoreController::class);
    Route::resource('scores.parts', ScorePartController::class)->shallow();
    Route::resource('concerts', ConcertController::class);
    Route::resource('users', UserController::class)->only(['index', 'create', 'store']);
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
