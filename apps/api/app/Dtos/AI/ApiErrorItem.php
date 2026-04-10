<?php

declare(strict_types=1);

namespace App\Dtos\AI;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ApiErrorItem extends Data
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $sourceId,
        public readonly string $type,
        public readonly string $error,
        public readonly array  $data = [],
    ) {}
}
