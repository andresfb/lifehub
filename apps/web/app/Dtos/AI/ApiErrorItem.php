<?php

declare(strict_types=1);

namespace App\Dtos\AI;

use Spatie\LaravelData\Data;

final class ApiErrorItem extends Data
{
    public function __construct(
        public readonly int $user_id,
        public readonly string $source_id,
        public readonly string $type,
        public readonly string $error,
        public readonly array $data = [],
    ) {}
}
