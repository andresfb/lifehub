<?php

declare(strict_types=1);

namespace App\Dtos\Media;

use Spatie\LaravelData\Data;

final class PageScreenshotItem extends Data
{
    public function __construct(
        public readonly int $modelId,
        public readonly string $url,
        public readonly string $collection,
    ) {}
}
