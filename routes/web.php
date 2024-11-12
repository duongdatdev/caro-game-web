<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\GameController;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\TestController;

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
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::post('/rooms/{room}/join', [RoomController::class, 'join'])->name('rooms.join');

    //Game routes
    Route::get('/game/{room}', [GameController::class, 'show'])->name('game.show');
    Route::post('/game/{room}/move', [GameController::class, 'makeMove'])->name('game.move');
    Route::post('/game/{room}/ready', [GameController::class, 'toggleReady'])->name('game.ready');

    //LeaderBoard routes
    Route::get('/leaderboard', function () {
        return Inertia::render('Leaderboard/Index');
    })->name('leaderboard');

    // Game History route
    Route::get('/history', function () {
        return Inertia::render('History/Index');
    })->name('history.index');

    //Event Trigger route
    Route::get('/trigger-event', [TestController::class, 'triggerEvent']);
    Route::post('/send-test-message', [TestController::class, 'sendTestMessage']);
});





require __DIR__.'/auth.php';
