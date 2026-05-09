<?php

declare(strict_types=1);

namespace Modules\Dashboard\Dtos;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class PinCreateItem extends Data
{
    /**
     * @param array<int, string>|null $tags
     */
    public function __construct(
        public readonly string $sectionSlug,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $description = null,
        public readonly ?string $icon = null,
        public readonly ?string $iconColor = null,
        public readonly ?array $tags = null,
    ) {}

    public function getUrl(): string
    {
        return str($this->url)
            ->trim()
            ->replaceEnd('/', '')
            ->value();
    }
}
