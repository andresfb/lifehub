<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ManifestItem extends Data
{
    /**
     * @param  Collection<int, ModuleManifest>  $modules
     */
    public function __construct(
        public readonly string $version,
        #[DataCollectionOf(ModuleManifest::class)]
        public readonly Collection $modules,
    ) {}
}
