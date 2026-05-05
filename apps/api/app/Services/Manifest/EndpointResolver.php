<?php

declare(strict_types=1);

namespace App\Services\Manifest;

use App\Dtos\Manifest\EndpointBinding;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

final class EndpointResolver
{
    public function resolve(EndpointBinding $binding): EndpointBinding
    {
        $route = Route::getRoutes()->getByName($this->qualifiedRouteName($binding->routeName));

        if ($route === null) {
            return $binding;
        }

        $methods = array_diff($route->methods(), ['HEAD']);
        $method = mb_strtoupper((string) ($methods[array_key_first($methods)] ?? 'GET'));
        $path = '/'.mb_ltrim($route->uri(), '/');

        return new EndpointBinding(
            routeName: $binding->routeName,
            type: $binding->type,
            method: $method,
            path: $path,
            operationId: $this->deriveOperationId($binding->routeName),
        );
    }

    /**
     * Module routes get an 'api.' prefix from RouteServiceProvider name() call.
     */
    private function qualifiedRouteName(string $routeName): string
    {
        return Str::startsWith($routeName, 'api.') ? $routeName : 'api.'.$routeName;
    }

    /**
     * Scramble strips the 'api.' prefix to produce the operationId.
     */
    private function deriveOperationId(string $routeName): string
    {
        return Str::startsWith($routeName, 'api.')
            ? Str::replaceFirst('api.', '', $routeName)
            : $routeName;
    }
}
