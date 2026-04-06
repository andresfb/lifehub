<?php

namespace App\Dtos\Bookmarks;

use Spatie\LaravelData\Data;

class MarkerRequest extends Data
{
    public function __construct(
        public readonly array $markers
    ) {}
}
