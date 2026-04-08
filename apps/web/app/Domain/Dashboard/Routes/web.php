<?php

declare(strict_types=1);

use App\Domain\Dashboard\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    'verified',
    'module.access:dashboard,read',
])
    ->prefix('dashboard')
    ->group(function () {

        Route::get('/', HomepageController::class)
            ->name('dashboard');

    });
