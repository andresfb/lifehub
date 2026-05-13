<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PinController;
use App\Http\Controllers\Dashboard\SearchProviderController;
use App\Http\Controllers\Search\SearchTermsController;
use App\Http\Controllers\Tags\SearchTagController;
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

    Route::get('/search/tags', SearchTagController::class)
        ->name('search.tags');

    Route::controller(SearchTermsController::class)->group(function () {
        Route::get('/search/terms', 'index')
            ->name('search.terms.index');

        Route::post('/search/terms', 'store')
            ->name('search.terms.store');
    });

    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'show'])
            ->name('dashboard');

        Route::controller(PinController::class)->group(function () {
            Route::get('/pins', 'index')
                ->name('dashboard.pins.index');

            Route::post('/pins', 'store')
                ->name('dashboard.pins.store');

            Route::delete('/pins/{pin}', 'destroy')
                ->name('dashboard.pins.destroy');

            Route::put('/pins/{pin}', 'update')
                ->name('dashboard.pins.update');

            Route::get('/pins/{pin}', 'show')
                ->name('dashboard.pins.show');
        });

        Route::controller(SearchProviderController::class)->group(function () {
            Route::get('/search-providers', 'index')
                ->name('dashboard.search-providers.index');

            Route::post('/search-providers', 'store')
                ->name('dashboard.search-providers.store');

            Route::put('/search-providers/{provider}', 'update')
                ->name('dashboard.search-providers.update');

            Route::delete('/search-providers/{provider}', 'destroy')
                ->name('dashboard.search-providers.delete');
        });

    });

    Route::delete('/logout', [LoginController::class, 'destroy'])
        ->name('logout');

});
