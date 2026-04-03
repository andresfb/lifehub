<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
    'module.access:bookmarks,read',
])
    ->prefix('bookmarks')
    ->group(function () {

        Route::get('/marker/{marker}', static function () {
            echo 'TODO: Change this to a Inertia component';
        })->name('bookmarks.marker.show');

    });
