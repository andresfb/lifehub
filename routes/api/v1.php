<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
|
| Routes for API version 1.
|
*/

Route::middleware(['auth:sanctum', 'throttle:authenticated'])->group(function (): void {

    Route::get('/user', static function (Request $request) {
        return $request->user();
    });

    // TODO: Change this to a API Controller
    Route::get('/reminder/{reminder}', static function (Request $request) {
        echo 'TODO: Change this to a API Controller';
    })->name('api.v1.reminder.show');

});

