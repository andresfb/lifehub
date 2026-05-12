<?php

declare(strict_types=1);

namespace App\Repository\Api\Dtos;

use Spatie\LaravelData\Data;

final class TagItem extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
    ) {}
}
