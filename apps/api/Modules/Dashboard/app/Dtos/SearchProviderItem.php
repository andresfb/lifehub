<?php

namespace Modules\Dashboard\Dtos;

use Spatie\LaravelData\Data;

class SearchProviderItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $url,
        public readonly string $term_field,
        public readonly bool $active,
        public readonly bool $default,
        public readonly ?int $order = null,
        public readonly ?string $icon = null,
        public readonly ?string $icon_color = null,
    ) {}

    public function getUrl(): string
    {
        return str($this->url)
            ->trim()
            ->lower()
            ->replaceEnd('/', '')
            ->value();
    }
}
