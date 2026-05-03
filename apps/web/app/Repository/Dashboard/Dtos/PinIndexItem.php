<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos;

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
     * @param  Collection<string, SectionItem>  $bookmarks
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
        public readonly string $storeAction,
        public readonly string $updateActionTemplate,
        public readonly string $deleteRouteName,
        public readonly string $searchTagsRouteName,
    ) {}
}
