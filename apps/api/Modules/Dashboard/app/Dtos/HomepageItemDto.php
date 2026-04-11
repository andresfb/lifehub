<?php

declare(strict_types=1);

namespace Modules\Dashboard\Dtos;

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
        public readonly int $order,
        public readonly ?string $description,
        public readonly ?string $color = null,
        public readonly ?string $image = null,
        public readonly ?array $tags = null,
    ) {}
}
