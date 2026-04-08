<?php

declare(strict_types=1);

namespace App\Domain\Core\Dtos;

use Spatie\LaravelData\Data;

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
