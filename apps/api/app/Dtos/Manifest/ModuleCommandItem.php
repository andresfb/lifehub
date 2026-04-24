<?php

namespace App\Dtos\Manifest;

use App\Enums\ModuleAccessLevel;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class ModuleCommandItem extends Data
{
    public function __construct(
        public readonly string $owner,
        public readonly string $code,
        public readonly string $name,
        public readonly ModuleAccessLevel $requiredAccess,
        public readonly EndpointBinding $endpoint,
        public readonly ?string $shortcut = null,
    ) {}
}
