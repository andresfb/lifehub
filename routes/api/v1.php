<?php

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
