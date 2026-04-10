<?php

declare(strict_types=1);

use App\Domain\Dashboard\Http\Controllers\Api\V1\PinController;
use App\Domain\Dashboard\Http\Controllers\Api\V1\DashboardController;
use App\Domain\Dashboard\Http\Controllers\Api\V1\MenuController;
use App\Domain\Dashboard\Http\Controllers\Api\V1\SearchProviderController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'module.access:dashboard,read',
])
    ->prefix('api/v1/dashboard')
    ->group(function (): void {

        Route::get('/', DashboardController::class)
            ->name('api.v1.dashboard');

        Route::get('/menu', MenuController::class)
            ->name('api.v1.dashboard.menu');

        Route::apiResource('/pins', PinController::class)
            ->names([
                'index' => 'api.v1.dashboard.pins.index',
                'store' => 'api.v1.dashboard.pins.store',
                'show' => 'api.v1.dashboard.pins.show',
                'update' => 'api.v1.dashboard.pins.update',
                'destroy' => 'api.v1.dashboard.pins.destroy',
            ]);

        Route::apiResource('/search/providers', SearchProviderController::class)
            ->names([
                'index' => 'api.v1.dashboard.search.providers.index',
                'store' => 'api.v1.dashboard.search.providers.store',
                'show' => 'api.v1.dashboard.search.providers.show',
                'update' => 'api.v1.dashboard.search.providers.update',
                'destroy' => 'api.v1.dashboard.search.providers.destroy',
            ]);

    });
