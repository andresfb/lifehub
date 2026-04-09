<?php

declare(strict_types=1);

use App\Domain\Dashboard\Http\Controllers\Api\V1\BookmarksController;
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

        Route::apiResource('/bookmarks', BookmarksController::class)
            ->names([
                'index' => 'api.v1.dashboard.bookmarks.index',
                'store' => 'api.v1.dashboard.bookmarks.store',
                'show' => 'api.v1.dashboard.bookmarks.show',
                'update' => 'api.v1.dashboard.bookmarks.update',
                'destroy' => 'api.v1.dashboard.bookmarks.destroy',
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
