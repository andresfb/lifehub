<?php

namespace App\Repository\Auth\Libraries;

use App\Models\User as UserModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use UnitEnum;

class AuthSession
{
    const string API_TOKEN_KEY = 'api_token';

    const string AUTH_USER_KEY = 'auth_user';

    const string TOKEN_HASH_KEY = 'token_hash';

    public static function has(string $key): bool
    {
        if (Session::has($key)) {
            return true;
        }

        if ($key !== self::API_TOKEN_KEY) {
            return false;
        }

        return request()->hasCookie(self::TOKEN_HASH_KEY);
    }

    public static function get(string $key, $default = null): mixed
    {
        if (Session::has($key)) {
            return Session::get($key, $default);
        }

        if ($key === self::API_TOKEN_KEY) {
            return self::getToken() ?? $default;
        }

        if ($key === self::AUTH_USER_KEY) {
            return self::getUser() ?? $default;
        }

        return $default;
    }

    public static function put(string $key, mixed $value): bool
    {
        Session::put($key, $value);

        if ($key === self::API_TOKEN_KEY) {
            return self::setToken($value);
        }

        if ($key === self::AUTH_USER_KEY) {
            return self::setUser($value);
        }

        return true;
    }

    public static function forget(array|string|UnitEnum $keys): bool
    {
        if (is_string($keys)) {
            $keys = [$keys];
        }

        foreach ($keys as $key) {
            if ($key === self::API_TOKEN_KEY) {
                self::deleteToken();
            }

            if ($key === self::AUTH_USER_KEY) {
                self::deleteUser();
            }
        }

        Session::forget($keys);

        return true;
    }

    public static function getTokenHash(string $token): string
    {
        return hash('sha256', sprintf(
            "USER:API:TOKEN:%s:%s",
            $token,
            Config::string('app.key'),
        ));
    }

    private static function getToken(): ?string
    {
        if (! request()->hasCookie(self::TOKEN_HASH_KEY)) {
            return null;
        }

        $hash = request()->cookie(self::TOKEN_HASH_KEY);
        $token = UserModel::getToken($hash);

        if (blank($token)) {
            return null;
        }

        Session::put(self::API_TOKEN_KEY, $token);

        return $token;
    }

    private static function setToken(mixed $value): bool
    {
        $token = (string) $value;
        $hash = self::getTokenHash($token);

        Cookie::queue(
            Cookie::make(
                name: self::TOKEN_HASH_KEY,
                value: $hash,
                minutes: (int) ceil((Config::integer('session.lifetime') * 1.5) / 60),
                path: Config::string('session.path'),
                domain: Config::string('session.domain'),
                secure: Config::boolean('session.encrypt'),
            )
        );

        return true;
    }

    private static function deleteToken(): void
    {
        self::deleteUser();

        Cookie::queue(Cookie::forget(self::TOKEN_HASH_KEY));
    }

    private static function getUser(): array
    {
        $token = self::get(self::API_TOKEN_KEY);
        if (blank($token)) {
            $token = self::getToken();
        }

        if (blank($token)) {
            return [];
        }

        return Cache::get(md5($token), []);
    }

    private static function setUser(mixed $value): bool
    {
        $token = self::get(self::API_TOKEN_KEY);
        if (blank($token)) {
            $token = self::getToken();
        }

        if (blank($token)) {
            return false;
        }

        return Cache::put(
            md5($token),
            (array) $value,
            Config::integer('session.lifetime') * 2
        );
    }

    private static function deleteUser(): void
    {
        $token = self::get(self::API_TOKEN_KEY);
        if (blank($token)) {
            $token = self::getToken();
        }

        if (blank($token)) {
            return;
        }

        UserModel::query()
            ->where(self::TOKEN_HASH_KEY, self::getTokenHash($token))
            ->delete();
    }
}
