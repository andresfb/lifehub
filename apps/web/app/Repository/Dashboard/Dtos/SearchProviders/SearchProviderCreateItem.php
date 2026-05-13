<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos\SearchProviders;

use Spatie\LaravelData\Data;

final class SearchProviderCreateItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly string $term_field,
        public readonly bool $default,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
    ) {}
}
