<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Dashboard\HomepageController;
use Illuminate\Support\Facades\Route;

Route::get('/', static function () {
    return to_route('dashboard');
})->name('home');

Route::middleware(['throttle:login'])->group(function () {

    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'show')
            ->name('login');

        Route::post('/login', 'store')
            ->name('login.store');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'show')
            ->name('register');

        Route::post('/register', 'store')
            ->name('register.store');
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

    Route::get('/dashboard', [HomepageController::class, 'show'])
        ->name('dashboard');

    Route::delete('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

});
