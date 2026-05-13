<?php

declare(strict_types=1);

namespace App\Repository\Dashboard\Dtos\SearchProviders;

use App\Repository\Manifest\Dtos\ModuleItem;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
final class SearchProviderIndexItem extends Data
{
    /**
     * @param Collection<string, SearchProviderItem> $searchEngines
     * @param Collection<int, ModuleItem> $modules
     */
    public function __construct(
        #[DataCollectionOf(SearchProviderItem::class)]
        public readonly Collection $searchEngines,
        #[DataCollectionOf(ModuleItem::class)]
        public readonly Collection $modules,
        public readonly bool $canCreate,
        public readonly bool $canEdit,
        public readonly bool $canDelete,
        public readonly string $storeAction,
        public readonly string $updateAction,
        public readonly string $deleteRouteName,
    ) {}
}
