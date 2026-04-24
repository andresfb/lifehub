<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

if (! function_exists('resolve_route')) {

    function resolve_route(string $webPath): string
    {
        $name = str($webPath)
            ->replaceFirst('/', '')
            ->replace('/', '.')
            ->value();

        $route = Route::getRoutes()->getByName($name);

        if ($route === null) {
            return $webPath;
        }

        return $route->uri();
    }
}
