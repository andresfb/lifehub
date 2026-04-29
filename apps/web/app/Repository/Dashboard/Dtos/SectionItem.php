<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SectionItem extends Data
{
    /**
     * @param  Collection<int, PinItem>  $items
     */
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
        public readonly int $order,
        #[DataCollectionOf(PinItem::class)]
        public readonly Collection $items,
    ) {}
}
