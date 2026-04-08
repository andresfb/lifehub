<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
    'module.access:core,read',
])
    ->group(function () {

        Route::get('/reminders/{reminder}', static function () {
            echo 'TODO: Change this to a Inertia component';
        })->name('reminder.show');

    });
