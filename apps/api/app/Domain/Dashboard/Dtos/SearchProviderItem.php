<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Dtos;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SearchProviderItem extends Data
{
    public function __construct(
        public readonly int $id = 0,
        public readonly int $userId = 0,
        public readonly string $name = '',
        public readonly string $url = '',
        public readonly int $order = 0,
    ) {}
}
