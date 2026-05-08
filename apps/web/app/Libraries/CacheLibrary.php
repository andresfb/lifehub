<?php

declare(strict_types=1);

namespace App\Libraries;

use Illuminate\Support\Facades\Cache;

final class CacheLibrary
{
    /**
     * clearCache Method.
     *
     * @param array<string, string>|int|null $user
     * @return void
     */
    public static function clearCache(array|int|null $user): void
    {
        Cache::tags(['manifest'])->flush();
        Cache::tags(['pins'])->flush();

        if (blank($user)) {
            return;
        }

        $userId = 0;
        if (is_array($user)) {
            $userId = array_key_exists('id', $user)
                ? (int) $user['id']
                : 0;
        } elseif (is_int($user)) {
            $userId = $user;
        }

        Cache::forget(md5("USER:MANIFEST:VERSION:{$userId}"));
    }
}
