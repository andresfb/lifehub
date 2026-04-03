<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Providers;

use App\Domain\Bookmarks\Enums\MorphTypes;
use App\Dtos\ModuleRecordItem;
use App\Dtos\MorphTypesItems;
use App\Enums\ModuleStatus;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

final class BookmarksServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('morph_types', function (Collection $types): void {
            $types->add(
                new MorphTypesItems(
                    MorphTypes::BOOKMARKS_MARKER->name,
                    MorphTypes::BOOKMARKS_MARKER->value,
                )
            );
        });

        $this->app->resolving('module_records', function (Collection $records): void {
            $records->add(
                new ModuleRecordItem(
                    key: 'BOOKMARKS',
                    name: 'Bookmarks',
                    description: 'A Bookmark Management System',
                    is_core: false,
                    is_public: true,
                    status: ModuleStatus::ACTIVE,
                )
            );
        });
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
