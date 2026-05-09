<?php

declare(strict_types=1);

namespace App\Repository\Core\Dtos\SearchHistory;

use Spatie\LaravelData\Data;

final class SearchTermActionItem extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $module,
        public readonly string $type,
        public readonly string $route,
    ) {}
}
