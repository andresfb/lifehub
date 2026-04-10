<?php

declare(strict_types=1);

namespace Modules\Dashboard\Dtos;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class HomepageSectionItem extends Data
{
    /**
     * @param  Collection<HomepageItemDto>|null  $items
     */
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $bgColor = null,
        public readonly ?Collection $items = null,
    ) {}
}
