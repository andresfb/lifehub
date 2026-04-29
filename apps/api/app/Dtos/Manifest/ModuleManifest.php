<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use App\Enums\ModuleKey;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class ModuleManifest extends Data
{
    /**
     * @param  Collection<int, ModuleCommandItem>|null  $commands
     * @param  Collection<int, ModuleActionItem>|null  $actions
     */
    public function __construct(
        public readonly ModuleKey $key,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $isPublic,
        public readonly NavigationItem $navigation,
        #[DataCollectionOf(ModuleCommandItem::class)]
        public readonly ?Collection $commands = null,
        #[DataCollectionOf(ModuleActionItem::class)]
        public readonly ?Collection $actions = null,
    ) {}
}
