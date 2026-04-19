<?php

declare(strict_types=1);

namespace Modules\Core\Dtos\AI;

use Laravel\Ai\Enums\Lab;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ResolvedUserAiProvider extends Data
{
    /**
     * @param  array<string, bool>  $featureCapabilities
     */
    public function __construct(
        public readonly string $name,
        public readonly string $code,
        public readonly Lab $lab,
        public readonly string $model,
        public readonly array $featureCapabilities,
    ) {}
}
