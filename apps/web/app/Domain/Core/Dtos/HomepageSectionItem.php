<?php

declare(strict_types=1);

namespace App\Domain\Core\Dtos;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class HomepageSectionItem extends Data
{
    /**
     * @param  Collection<HomepageItemDto>|null  $items
     */
    public function __construct(
        public readonly int $id,
        public readonly int $user_id,
        public readonly string $slug,
        public readonly string $name,
        public readonly ?string $bg_color = null,
        public readonly ?Collection $items = null,
    ) {}
}
