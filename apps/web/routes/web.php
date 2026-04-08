<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // TODO: Module access example 👇
    //    Route::prefix('journal')
    //        ->middleware(['module.access:journal,read'])
    //        ->group(function () {
    //            Route::get('/', [JournalController::class, 'index']);
    //            Route::post('/', [JournalController::class, 'store'])
    //                ->middleware('module.access:journal,write');
    //        });
});

require __DIR__.'/settings.php';
