<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GlobalSearchController;
use App\Http\Controllers\Api\V1\ManifestController;
use App\Http\Controllers\Api\V1\SearchTagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

Route::name('api.v1.')->group(function (): void {
    // Public routes with auth rate limiter (5/min - brute force protection)
    Route::middleware(['throttle:auth'])->group(function (): void {
        Route::post('register', [AuthController::class, 'register'])
            ->name('register');

        Route::post('login', [AuthController::class, 'login'])
            ->name('login');

        Route::post('login/validate', [AuthController::class, 'validateTwoFactorCode'])
            ->name('login.validate');
    });

    // Protected routes with authenticated rate limiter (120/min)
    Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
        Route::get('me', [AuthController::class, 'me'])
            ->name('me');

        Route::controller(ManifestController::class)->group(function () {
            Route::get('manifesto', 'index')
                ->name('manifesto');

            Route::get('manifesto/version', 'show')
                ->name('manifesto.version');
        });

        Route::get('search', GlobalSearchController::class)
            ->name('search');

        Route::get('search/tags', SearchTagController::class)
            ->name('search.tags');

        Route::post('logout', [AuthController::class, 'logout'])
            ->name('logout');

    });

    // Password reset routes (public with rate limiting)
    Route::middleware(['throttle:6,1'])->group(function (): void {
        Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('password.email');

        Route::post('reset-password', [AuthController::class, 'resetPassword'])
            ->name('password.reset');
    });
});
