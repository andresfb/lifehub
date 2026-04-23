<?php

declare(strict_types=1);

namespace App\Dtos\Manifest;

use App\Enums\ModuleAccessLevel;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class FeatureNode extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly ModuleAccessLevel $requiredAccess = ModuleAccessLevel::READ,
        public readonly MenuItem $menuItem,
        #[DataCollectionOf(__CLASS__)]
        public readonly ?Collection $nodes = null,
    ) {}
}
