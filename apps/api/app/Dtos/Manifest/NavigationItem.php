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
final class NavigationItem extends Data
{
    /**
     * @param  Collection<int, NavigationItem>|null  $nodes
     */
    public function __construct(
        public readonly string $id,
        public readonly ModuleKey $key,
        public readonly string $name,
        public readonly string $webPath,
        public readonly string $icon,
        public readonly ?string $shortcut = null,
        public readonly bool $show = false,
        #[DataCollectionOf(self::class)]
        public readonly ?Collection $nodes = null,
    ) {}
}
