<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Dtos;

use Override;
use Spatie\LaravelData\Data;

final class MarkerItem extends Data
{
    public function __construct(
        public readonly int $category_id,
        public readonly string $title,
        public readonly string $url,
        public readonly string $tags = '',
    ) {}

    #[Override]
    public function toArray(): array
    {
        $data = parent::toArray();
        unset($data['tags']);

        return $data;
    }
}
