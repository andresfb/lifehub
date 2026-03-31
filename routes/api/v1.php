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
Route::get('/user', static function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
