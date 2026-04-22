<?php

namespace App\Repository\Auth\Libraries;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use UnitEnum;

class AuthSession
{
    public static function has(string $key): bool
    {
        if (Session::has($key)) {
            return true;
        }

        // TODO: cannot use cache with the key as-is. This is not user segregated so there will be record clashes.
        if (Cache::has($key)) {
            return true;
        }

        return false;
    }

    public static function get(array|string|UnitEnum $key, $default = null): mixed
    {
        if (Session::has($key)) {
            return Session::get($key, $default);
        }

        if (! Cache::has($key)) {
            return $default;
        }

        $value = Cache::get($key, $default);
        if ($value === $default) {
            return $value;
        }

        Session::put($key, $value);

        return $value;
    }

    public static function put(string $key, mixed $value): bool
    {
        Session::put($key, $value);

        return static::putOnCache($key, $value);
    }

    public static function forget(array|string|UnitEnum $keys): bool
    {
        Session::forget($keys);

        if (is_string($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return true;
    }

    private static function putOnCache(string $key, mixed $value): bool
    {
        return Cache::put(
            $key,
            $value,
            Config::integer('session.lifetime') * 2
        );
    }
}
