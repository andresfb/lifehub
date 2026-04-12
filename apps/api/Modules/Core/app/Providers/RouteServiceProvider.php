<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Override;

final class RouteServiceProvider extends ServiceProvider
{
    private string $name = 'Core';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    #[Override]
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     */
    private function mapApiRoutes(): void
    {
        Route::middleware('api')->prefix('api')->name('api.')
            ->group(module_path($this->name, '/routes/api/v1.php'));
    }
}
