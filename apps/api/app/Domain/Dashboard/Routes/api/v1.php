<?php

declare(strict_types=1);

use App\Domain\Dashboard\Http\Controllers\Api\V1\HomepageController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'module.access:dashboard,read',
])
    ->prefix('api/v1/dashboard')
    ->group(function (): void {

        Route::get('/dashboard', HomepageController::class)
            ->name('api.v1.dashboard');

    });
