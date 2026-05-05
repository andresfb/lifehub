<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GlobalSearchController;
use App\Http\Controllers\Api\V1\ManifestController;
use App\Http\Controllers\Api\V1\SearchTagController;
use Illuminate\Support\Facades\Route;
use Spatie\ResponseCache\Middlewares\CacheResponse;
use function Illuminate\Support\hours;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

// Public routes with auth rate limiter (5/min - brute force protection)
Route::middleware(['throttle:auth'])->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])
        ->name('api.v1.register');

    Route::post('login', [AuthController::class, 'login'])
        ->name('api.v1.login');

    Route::post('login/validate', [AuthController::class, 'validateTwoFactorCode'])
        ->name('api.v1.login.validate');
});

// Protected routes with authenticated rate limiter (120/min)
Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
    Route::get('me', [AuthController::class, 'me'])
        ->name('api.v1.me')
        ->middleware(
            CacheResponse::for(
                lifetime: hours(8),
                tags: ['user']
            )
        );

    Route::controller(ManifestController::class)->group(function () {
        Route::get('manifesto', 'index')
            ->name('api.v1.manifesto');

        Route::get('manifesto/version', 'show')
            ->name('api.v1.manifesto.version');
    });

    Route::get('search', GlobalSearchController::class)
        ->name('api.v1.search');

    Route::get('search/tags', SearchTagController::class)
        ->name('api.v1.search.tags');

    Route::post('logout', [AuthController::class, 'logout'])
        ->name('api.v1.logout');

});

// Password reset routes (public with rate limiting)
Route::middleware(['throttle:6,1'])->group(function (): void {
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('api.v1.password.email');

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('api.v1.password.reset');
});
