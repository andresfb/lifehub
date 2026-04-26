<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Dtos;

use Illuminate\Support\Facades\Config;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class EndpointItem extends Data
{
    public function __construct(
        public readonly string $routeName,
        public readonly ?string $method = null,
        public readonly ?string $path = null,
        public readonly ?string $operationId = null,
    ) {}

    public function getUri(): string
    {
        return sprintf(
            '%s%s',
            Config::string('services.backend.host'),
            $this->path
        );
    }
}
