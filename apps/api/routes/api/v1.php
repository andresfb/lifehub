<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GlobalSearchController;
use App\Http\Controllers\Api\V1\ManifestController;
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
Route::middleware(['throttle:auth', 'idempotency'])->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])
        ->name('api.v1.register');

    Route::post('login', [AuthController::class, 'login'])
        ->name('api.v1.login');
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

    Route::get('manifesto', ManifestController::class)
        ->name('api.v1.manifesto')
        ->middleware(
            CacheResponse::for(
                lifetime: hours(8),
                tags: ['manifest']
            )
        );

    Route::get('search', GlobalSearchController::class)
        ->name('api.v1.search');

    Route::middleware('idempotency')->group(function (): void {
        Route::post('logout', [AuthController::class, 'logout'])
            ->name('api.v1.logout');

        // Email verification
        Route::post('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
            ->middleware('signed')
            ->name('api.v1.verification.verify');

        Route::post('email/resend', [AuthController::class, 'resendVerificationEmail'])
            ->middleware('throttle:6,1')
            ->name('api.v1.verification.send');
    });
});

// Password reset routes (public with rate limiting)
Route::middleware(['throttle:6,1', 'idempotency'])->group(function (): void {
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('api.v1.password.email');

    Route::post('reset-password', [AuthController::class, 'resetPassword'])
        ->name('api.v1.password.reset');
});
