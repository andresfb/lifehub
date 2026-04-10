<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Http\Controllers\Api\V1\DashboardController;
use Modules\Dashboard\Http\Controllers\Api\V1\MenuController;
use Modules\Dashboard\Http\Controllers\Api\V1\PinController;
use Modules\Dashboard\Http\Controllers\Api\V1\SearchProviderController;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'can:module.dashboard.read',
])
    ->prefix('v1/dashboard')
    ->group(function (): void {

        Route::get('/', DashboardController::class)
            ->name('v1.dashboard');

        Route::get('/menu', MenuController::class)
            ->name('v1.dashboard.menu')
            ->middleware('idempotency');

        Route::apiResource('/pins', PinController::class)
            ->middlewareFor('index', 'idempotency')
            ->names([
                'index' => 'v1.dashboard.pins.index',
                'store' => 'v1.dashboard.pins.store',
                'show' => 'v1.dashboard.pins.show',
                'update' => 'v1.dashboard.pins.update',
                'destroy' => 'v1.dashboard.pins.destroy',
            ]);

        Route::apiResource('/search/providers', SearchProviderController::class)
            ->middlewareFor('index', 'idempotency')
            ->names([
                'index' => 'v1.dashboard.search.providers.index',
                'store' => 'v1.dashboard.search.providers.store',
                'show' => 'v1.dashboard.search.providers.show',
                'update' => 'v1.dashboard.search.providers.update',
                'destroy' => 'v1.dashboard.search.providers.destroy',
            ]);

    });
