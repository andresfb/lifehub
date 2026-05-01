<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Manifest\Dtos\ModuleItem;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class PinIndexItem extends Data
{
    /**
     * @param  array<string, string>  $sections
     * @param  Collection<int, SectionItem>  $bookmarks
     * @param  Collection<int, ModuleItem>  $modules
     */
    public function __construct(
        public readonly array $sections,
        #[DataCollectionOf(SectionItem::class)]
        public readonly Collection $bookmarks,
        #[DataCollectionOf(ModuleItem::class)]
        public readonly Collection $modules,
        public readonly bool $canCreate,
        public readonly bool $canEdit,
        public readonly bool $canDelete,
        public readonly string $createRouteName,
        public readonly string $updateRouteName,
        public readonly string $deleteRouteName,
    ) {}
}
