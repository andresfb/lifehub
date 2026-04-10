<?php

declare(strict_types=1);

namespace App\Libraries\MediaLibrary;

final readonly class MediaNamesLibrary
{
    public static function encrypted(): string
    {
        return 'encrypted';
    }

    public static function image(): string
    {
        return 'image';
    }

    public static function video(): string
    {
        return 'video';
    }

    public static function document(): string
    {
        return 'document';
    }
}
