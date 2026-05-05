<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use App\Enums\ModuleEndpointType;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class EndpointBinding extends Data
{
    public function __construct(
        public readonly string $routeName,
        public readonly ModuleEndpointType $type = ModuleEndpointType::ACTION,
        public readonly ?string $method = null,
        public readonly ?string $path = null,
        public readonly ?string $operationId = null,
    ) {}
}
