<?php

declare(strict_types=1);

namespace App\Dtos;

use Spatie\LaravelData\Data;

final class PageActionItem extends Data
{
    public function __construct(
        public readonly string $label,
        public readonly string $route,
        public readonly string $icon,
    ) {}
}
