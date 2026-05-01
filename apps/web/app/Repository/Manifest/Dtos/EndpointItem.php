<?php

declare(strict_types=1);

namespace App\Repository\Manifest\Dtos;

use Illuminate\Support\Facades\Config;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

use function sprintf;

#[MapName(SnakeCaseMapper::class)]
final class EndpointItem extends Data
{
    public function __construct(
        public readonly string $routeName,
        public readonly string $type,
        public readonly ?string $method = null,
        public readonly ?string $path = null,
        public readonly ?string $operationId = null,
    ) {}

    public function getUri(): string
    {
        $path = str($this->path);
        if ($path->contains('{') || $path->contains('}')) {
            $path = $path->dirname();
        }

        return sprintf(
            '%s%s',
            Config::string('services.backend.host'),
            $path->value(),
        );
    }
}
