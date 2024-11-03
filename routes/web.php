<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    //Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Room routes
    Route::get('/rooms', function () {
        return Inertia::render('Room/Index');
    })->name('rooms.index');

    //Game routes
    Route::get('/game/{id}', function () {
        return Inertia::render('Game/Show');
    })->name('game.show');

    //LeaderBoard routes
    Route::get('/leaderboard', function () {
        return Inertia::render('Leaderboard/Index');
    })->name('leaderboard');

    // Game History route
    Route::get('/history', function () {
        return Inertia::render('History/Index');
    })->name('history.index');
});

require __DIR__.'/auth.php';
