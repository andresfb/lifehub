<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // TODO: Change this to a Inertia component
    Route::get('/reminders/{reminder}', static function () {
        echo 'TODO: Change this to a Inertia component';
    })->name('reminder.show');

});

require __DIR__.'/settings.php';
