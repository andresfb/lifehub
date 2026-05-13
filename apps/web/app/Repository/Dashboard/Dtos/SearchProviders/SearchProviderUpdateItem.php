<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos\SearchProviders;

use Spatie\LaravelData\Data;

final class SearchProviderUpdateItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly string $term_field,
        public readonly bool $active,
        public readonly bool $default,
        public readonly int $order,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
    ) {}
}
