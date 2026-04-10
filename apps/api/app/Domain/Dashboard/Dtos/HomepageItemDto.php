<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Dtos;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class HomepageItemDto extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $image = null,
        public readonly ?array $tags = null,
    ) {}
}
