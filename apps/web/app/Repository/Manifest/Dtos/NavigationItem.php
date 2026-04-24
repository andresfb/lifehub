<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Dtos;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class NavigationItem extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $web_path,
        public readonly string $icon,
        public readonly bool $show,
        public readonly int $sort_order,
        public readonly ?string $shortcut = null,
        #[DataCollectionOf(self::class)]
        public readonly ?Collection $children = null,
    ) {}
}
