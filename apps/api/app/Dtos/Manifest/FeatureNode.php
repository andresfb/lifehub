<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use App\Enums\FeatureKind;
use App\Enums\ModuleAccessLevel;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class FeatureNode extends Data
{
    /**
     * @param  Collection<int, EndpointBinding>|null  $endpoints
     * @param  Collection<int, FeatureNode>|null  $children
     */
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly FeatureKind $kind,
        public readonly ModuleAccessLevel $requiredAccess = ModuleAccessLevel::READ,
        public readonly ?NavHints $nav = null,
        #[DataCollectionOf(EndpointBinding::class)]
        public readonly ?Collection $endpoints = null,
        #[DataCollectionOf(self::class)]
        public readonly ?Collection $children = null,
    ) {}
}
