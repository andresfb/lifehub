<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class MorphTypesItems extends Data
{
    public function __construct(
        public readonly string $key,
        public readonly string $class,
    ) {}
}
