<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Api\V1\ReminderController;

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'can:module.core.read',
])
    ->prefix('v1')
    ->group(function (): void {

        Route::get('/reminder/{reminder}', ReminderController::class)
            ->name('v1.reminder.show');

    });
