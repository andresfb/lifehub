<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

trait HasSlug
{
    /**
     * @throws Exception
     */
    protected static function bootHasSlug(): void
    {
        static::creating(static function (Model $model) {
            $slugField = self::getFieldName();
            $model->$slugField = self::createSlug();
        });
    }

    /**
     * @throws Exception
     */
    protected static function createSlug(): string
    {
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $slug = self::base62Encode(random_bytes(8));

            if (static::where('slug', $slug)->exists()) {
                continue;
            }

            return $slug;
        }

        throw new RuntimeException("Failed to generate unique slug after {$maxAttempts} attempts");
    }

    protected static function base62Encode(string $input): string
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = mb_strlen($chars);
        $result = '';

        $num = gmp_init(bin2hex($input), 16);
        while (gmp_cmp($num, 0) > 0) {
            [$num, $rem] = gmp_div_qr($num, $base);
            $result .= $chars[gmp_intval($rem)];
        }

        return mb_str_pad($result, 11, '0', STR_PAD_LEFT);
    }

    protected static function getFieldName(): string
    {
        return 'slug';
    }
}
