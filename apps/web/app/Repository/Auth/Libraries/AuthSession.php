<?php

namespace App\Repository\Auth\Libraries;

use DateInterval;
use DateTimeInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use UnitEnum;

class AuthSession
{
    public static function has(string $key): bool
    {
        if (Cache::has($key)) {
            return true;
        }

        if (Session::has($key)) {
            return true;
        }

        return false;
    }

    public static function get(array|string|UnitEnum $key, $default = null): mixed
    {
        if (Cache::has($key)) {
            return Cache::get($key, $default);
        }

        return Session::get($key, $default);
    }

    public static function put(string $key, mixed $value, DateInterval|DateTimeInterface|int|null $ttl = null): bool
    {
        Session::put($key, $value);

        if (blank($ttl)) {
            $ttl = now()->addDay();
        }

        return Cache::put($key, $value, $ttl);
    }

    public static function forget(array|string|UnitEnum $keys): bool
    {
        Session::forget($keys);

        return Cache::forget($keys);
    }
}
