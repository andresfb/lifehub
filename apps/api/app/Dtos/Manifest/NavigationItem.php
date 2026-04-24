<?php

namespace App\Dtos\Manifest;

use App\Enums\ModuleKey;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class NavigationItem extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly ModuleKey $key,
        public readonly string $name,
        public readonly string $webPath,
        public readonly string $icon,
        public readonly ?string $shortcut = null,
        public readonly bool $show = false,
        #[DataCollectionOf(__CLASS__)]
        public readonly ?Collection $nodes = null,
    ) {}
}
