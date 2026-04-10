<?php

declare(strict_types=1);

namespace App\Dtos\Media;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class PageScreenshotItem extends Data
{
    public function __construct(
        public readonly int $modelId,
        public readonly string $url,
        public readonly string $collection,
    ) {}
}
