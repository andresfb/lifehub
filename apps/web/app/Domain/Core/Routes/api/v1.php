<?php

declare(strict_types=1);

Route::middleware([
    'auth:sanctum',
    'throttle:authenticated',
    'module.access:core,read',
])
    ->prefix('api/v1')
    ->group(function (): void {

        // TODO: Change this to a API Controller
        Route::get('/reminder/{reminder}', static function (Request $request) {
            echo 'TODO: Change this to a API Controller';
        })->name('api.v1.reminder.show');

    });
