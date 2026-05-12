<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\V1\ReminderController;
use Modules\Core\Http\Controllers\Api\V1\SearchHistoryController;
use Modules\Core\Http\Controllers\Api\V1\UserAiModelController;
use Modules\Core\Http\Controllers\Api\V1\UserAiProviderController;
use Spatie\ResponseCache\Middlewares\CacheResponse;

use function Illuminate\Support\minutes;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'can:module.core.read',
])
    ->prefix('v1')
    ->name('v1.')
    ->group(function (): void {

        Route::controller(SearchHistoryController::class)->group(function () {
            Route::get('search/terms', 'index')
                ->name('search.terms')
                ->middleware(
                    CacheResponse::for(
                        lifetime: minutes(10),
                        tags: ['search-terms']
                    )
                );

            Route::middleware('can:module.core.write')->group(function (): void {
                Route::post('search/history', 'store')
                    ->name('search.history.store');

                Route::delete('search/history/{searchHistory}', 'destroy')
                    ->name('search.history.destroy');
            });
        });

        Route::get('/reminder/{reminder}', ReminderController::class)
            ->name('reminder.show');

        Route::get('/ai/providers', [UserAiProviderController::class, 'index'])
            ->name('ai.providers.index');

        Route::get('/ai/providers/{provider}', [UserAiProviderController::class, 'show'])
            ->name('ai.providers.show');

        Route::post('/ai/providers', [UserAiProviderController::class, 'store'])
            ->name('ai.providers.store');

        Route::patch('/ai/providers/{provider}', [UserAiProviderController::class, 'update'])
            ->name('ai.providers.update');

        Route::delete('/ai/providers/{provider}', [UserAiProviderController::class, 'destroy'])
            ->name('ai.providers.destroy');

        Route::post('/ai/providers/{provider}/models', [UserAiProviderController::class, 'storeModel'])
            ->name('ai.providers.models.store');

        Route::get('/ai/models/{model}', [UserAiModelController::class, 'show'])
            ->name('ai.models.show');

        Route::patch('/ai/models/{model}', [UserAiModelController::class, 'update'])
            ->name('ai.models.update');

        Route::delete('/ai/models/{model}', [UserAiModelController::class, 'destroy'])
            ->name('ai.models.destroy');

    });
