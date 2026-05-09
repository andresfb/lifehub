<?php

declare(strict_types=1);

namespace App\Repository\Core\Dtos\SearchHistory;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class SearchTermItem extends Data
{
    public function __construct(
        public readonly string $module,
        public readonly string $type,
        #[MapInputName('query')]
        public readonly string $term,
    ) {}
}
