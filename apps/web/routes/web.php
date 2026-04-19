<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return redirect()->route('dashboard');
})->name('home');

Route::middleware(['throttle:login'])->group(function () {

    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'show')
            ->name('login');

        Route::post('/login', 'store')
            ->name('login.store');
    });

});

Route::middleware(['throttle:two-factor'])->group(function () {

    Route::controller(TwoFactorController::class)->group(function () {
        Route::get('/two-factor', 'show')
            ->name('login.two-factor.show');

        Route::post('/two-factor', 'store')
            ->name('login.two-factor.store');
    });

});

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', static function () {
        echo 'dashboard';
    })->name('dashboard');

});
