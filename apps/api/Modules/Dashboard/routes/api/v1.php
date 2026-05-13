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
    ->name('v1.dashboard.')
    ->group(function (): void {

        Route::get('/', DashboardController::class)
            ->name('v1.dashboard');

        Route::apiResource('/pins', PinController::class)
            ->middlewareFor(
                ['index'],
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            )
            ->middlewareFor(
                ['store', 'update', 'destroy'],
                ['can:module.dashboard.write']
            )
            ->names([
                'index' => 'pins.index',
                'store' => 'pins.store',
                'update' => 'pins.update',
                'destroy' => 'pins.destroy',
            ]);

        Route::get('/pins/search', SearchPinController::class)
            ->name('pins.search')
            ->middleware(
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            );

        Route::apiResource('/search-providers', SearchProviderController::class)
            ->middlewareFor(
                ['index'],
                CacheResponse::for(
                    lifetime: hours(8),
                    tags: ['dashboard']
                )
            )
            ->middlewareFor(
                ['store', 'update', 'destroy'],
                ['can:module.dashboard.write']
            )
            ->names([
                'index' => 'search.providers.index',
                'store' => 'search.providers.store',
                'update' => 'search.providers.update',
                'destroy' => 'search.providers.destroy',
            ]);

    });
