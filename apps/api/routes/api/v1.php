<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TokenAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

// Token auth for non-browser clients (TUI, Desktop, Mobile)
Route::middleware('throttle:auth')->group(function (): void {
    Route::post('auth/token', [TokenAuthController::class, 'store'])->name('api.v1.auth.token');
});

// Protected routes (works with both session cookie auth and Bearer token auth)
Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {
    Route::get('me', [AuthController::class, 'me'])->name('api.v1.me');
    Route::delete('auth/token', [TokenAuthController::class, 'destroy'])->name('api.v1.auth.token.destroy');
});
