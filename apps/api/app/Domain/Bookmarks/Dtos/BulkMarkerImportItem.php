<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Dtos;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class BulkMarkerImportItem extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $category,
        public readonly string $status,
        public readonly string $title,
        public readonly string $url,
        public readonly string $domain = '',
        public readonly ?string $notes = null,
        public readonly ?array $tags = null,
    ) {}
}
