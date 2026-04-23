<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\V1\ReminderController;
use Modules\Core\Http\Controllers\Api\V1\UserAiModelController;
use Modules\Core\Http\Controllers\Api\V1\UserAiProviderController;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'can:module.core.read',
])
    ->prefix('v1')
    ->group(function (): void {

        Route::get('/reminder/{reminder}', ReminderController::class)
            ->name('v1.reminder.show');

        Route::get('/ai/providers', [UserAiProviderController::class, 'index'])
            ->name('v1.ai.providers.index');

        Route::get('/ai/providers/{provider}', [UserAiProviderController::class, 'show'])
            ->name('v1.ai.providers.show');

        Route::post('/ai/providers', [UserAiProviderController::class, 'store'])
            ->name('v1.ai.providers.store');

        Route::patch('/ai/providers/{provider}', [UserAiProviderController::class, 'update'])
            ->name('v1.ai.providers.update');

        Route::delete('/ai/providers/{provider}', [UserAiProviderController::class, 'destroy'])
            ->name('v1.ai.providers.destroy');

        Route::post('/ai/providers/{provider}/models', [UserAiProviderController::class, 'storeModel'])
            ->name('v1.ai.providers.models.store');

        Route::get('/ai/models/{model}', [UserAiModelController::class, 'show'])
            ->name('v1.ai.models.show');

        Route::patch('/ai/models/{model}', [UserAiModelController::class, 'update'])
            ->name('v1.ai.models.update');

        Route::delete('/ai/models/{model}', [UserAiModelController::class, 'destroy'])
            ->name('v1.ai.models.destroy');

    });
