<?php

declare(strict_types=1);

namespace App\Dtos\Modules;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class MorphTypesItems extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $class,
    ) {}
}
