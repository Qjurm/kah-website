<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Music\MusicController;
use App\Http\Controllers\MusicianDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScoreController;
use App\Http\Controllers\Admin\ScorePartController;
use App\Http\Controllers\Admin\ConcertController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InstrumentController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Musician Dashboard
Route::middleware(['auth', 'musician'])->get('/mijn-instrument', [MusicianDashboardController::class, 'index'])->name('mijn-instrument');

// Music (auth + musician/admin)
Route::middleware(['auth', 'musician'])->group(function () {
    Route::get('/muziek', [MusicController::class, 'index'])->name('muziek.index');
    Route::get('/muziek/bladmuziek/{score}/partijen/{part}/download', [MusicController::class, 'download'])->name('muziek.download');
    
    // Redirect routes for dual-role users
    Route::get('/muziek-home', function () {
        return redirect()->route('muziek.index');
    });
});

// Admin (Admin Panel) - URLs stay Dutch for users
Route::middleware(['auth', 'admin'])->prefix('beheer')->name('beheer.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('bladmuziek', ScoreController::class);
    Route::post('bladmuziek/{score}/partijen', [ScorePartController::class, 'store'])->name('bladmuziek.partijen.store');
    Route::delete('partijen/{part}', [ScorePartController::class, 'destroy'])->name('partijen.destroy');
    Route::resource('concerten', ConcertController::class);
    Route::resource('gebruikers', UserController::class)->only(['index', 'create', 'store']);
    Route::put('gebruikers/{user}/approve', [UserController::class, 'approve'])->name('gebruikers.approve');
    Route::delete('gebruikers/{user}', [UserController::class, 'destroy'])->name('gebruikers.destroy');
    
    // API endpoint for inline instrument creation
    Route::post('/api/instruments', [InstrumentController::class, 'store'])->name('api.instruments.store');
});

// Redirect route for dual-role users returning from musician mode to admin
Route::middleware(['auth', 'admin'])->get('/admin-home', function () {
    return redirect()->route('beheer.dashboard');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/instruments', [ProfileController::class, 'updateInstruments'])->name('profile.instruments.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
