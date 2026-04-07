<?php

namespace App\Dtos\Media;

use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\HasMedia;

class PageScreenshotItem extends Data
{
    public function __construct(
        public readonly int $modelId,
        public readonly string $url,
        public readonly string $collection,
    ) {}
}
