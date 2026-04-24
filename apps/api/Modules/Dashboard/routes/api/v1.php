<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\Api\V1\DashboardController;
use Modules\Dashboard\Http\Controllers\Api\V1\PinController;
use Modules\Dashboard\Http\Controllers\Api\V1\SearchPinController;
use Modules\Dashboard\Http\Controllers\Api\V1\SearchProviderController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

use function Illuminate\Support\hours;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'can:module.dashboard.read',
])
    ->prefix('v1/dashboard')
    ->group(function (): void {

        Route::get('/', DashboardController::class)
            ->name('v1.dashboard');

        Route::apiResource('/pins', PinController::class)
            ->middlewareFor(
                ['index', 'show'],
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            )
            ->names([
                'index' => 'v1.dashboard.pins.index',
                'store' => 'v1.dashboard.pins.store',
                'show' => 'v1.dashboard.pins.show',
                'update' => 'v1.dashboard.pins.update',
                'destroy' => 'v1.dashboard.pins.destroy',
            ]);

        Route::get('/pins/search', SearchPinController::class)
            ->name('v1.dashboard.pins.search')
            ->middleware(
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            );

        Route::apiResource('/search/providers', SearchProviderController::class)
            ->middlewareFor(
                ['index', 'show'],
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            )
            ->names([
                'index' => 'v1.dashboard.search.providers.index',
                'store' => 'v1.dashboard.search.providers.store',
                'show' => 'v1.dashboard.search.providers.show',
                'update' => 'v1.dashboard.search.providers.update',
                'destroy' => 'v1.dashboard.search.providers.destroy',
            ]);

    });
