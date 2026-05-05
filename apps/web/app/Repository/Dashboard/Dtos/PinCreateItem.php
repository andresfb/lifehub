<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

use Spatie\LaravelData\Data;

final class PinCreateItem extends Data
{
    /**
     * @param  array<int, string>  $tags
     */
    public function __construct(
        public readonly string $section_slug,
        public readonly string $title,
        public readonly string $url,
        public readonly ?array $tags = null,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
        public readonly ?string $description = null,
    ) {}
}
