<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class MorphTypesItems extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $class,
    ) {}
}
