<?php

declare(strict_types=1);

use App\Domain\Core\Http\Controllers\HomepageController;

Route::middleware([
    'auth',
    'verified',
    'module.access:core,read',
])
    ->group(function () {

        Route::get('dashboard', HomepageController::class)
            ->name('dashboard');

        Route::get('/reminders/{reminder}', static function () {
            echo 'TODO: Change this to a Inertia component';
        })->name('reminder.show');

    });
