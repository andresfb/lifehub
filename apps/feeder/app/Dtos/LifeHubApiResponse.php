<?php

namespace App\Dtos;

use Spatie\LaravelData\Data;

class LifeHubApiResponse extends Data
{
    public function __construct(
        public readonly bool $success = false,
        public readonly string $message = '',
        public readonly ?array $data = null,
    ) {}
}
