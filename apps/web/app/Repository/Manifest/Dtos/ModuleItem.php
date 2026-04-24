<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Dtos;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ModuleItem extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $isPublic,
        public readonly int $sortOrder,
        #[DataCollectionOf(NavigationItem::class)]
        public readonly ?Collection $navigation = null,
    ) {}
}
