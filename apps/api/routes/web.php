<?php

declare(strict_types=1);

// Web routes disabled - API only application
// Scramble documentation available at /docs/api

use Illuminate\Support\Facades\Route;

// Required so Laravel's ResetPassword notification can generate a valid URL.
// ResetPassword::createUrlUsing() in FortifyServiceProvider redirects to the SPA instead.
Route::get('/reset-password/{token}', static fn () => abort(404))->name('password.reset');
