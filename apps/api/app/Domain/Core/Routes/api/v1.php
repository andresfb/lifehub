<?php

declare(strict_types=1);

use App\Domain\Core\Http\Controllers\Api\V1\ReminderController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'module.access:core,read',
])
    ->prefix('api/v1')
    ->group(function (): void {

        Route::get('/reminder/{reminder}', ReminderController::class)
            ->name('api.v1.reminder.show');

    });
