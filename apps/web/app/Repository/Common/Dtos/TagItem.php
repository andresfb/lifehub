<?php

namespace App\Repository\Common\Dtos;

use Spatie\LaravelData\Data;

class TagItem extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $name,
    ) {}
}
