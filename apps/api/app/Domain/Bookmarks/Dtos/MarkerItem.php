<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Dtos;

use Override;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class MarkerItem extends Data
{
    public function __construct(
        public readonly int $categoryId,
        public readonly string $title,
        public readonly string $url,
        public readonly string $tags = '',
    ) {}

    #[Override]
    public function toArray(): array
    {
        return $this->except('tags')
            ->toArray();
    }
}
