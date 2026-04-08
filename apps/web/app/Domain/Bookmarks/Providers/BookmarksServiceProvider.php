<?php

declare(strict_types=1);

namespace App\Domain\Bookmarks\Providers;

use App\Domain\Bookmarks\Commands\CreateMarkerCommand;
use App\Domain\Bookmarks\Enums\MorphTypes;
use App\Dtos\Modules\ModuleRecordItem;
use App\Dtos\Modules\MorphTypesItems;
use App\Enums\ModuleStatus;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class BookmarksServiceProvider extends ServiceProvider
{
    private array $commands = [
        CreateMarkerCommand::class,
    ];

    public function register(): void
    {
        $this->commands($this->commands);

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
        $this->mergeConfigFrom(__DIR__.'/../Configs/config.php', 'markers');
        $this->mergeConfigFrom(__DIR__.'/../Configs/typesense.php', 'scout.typesense.model-settings');

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/web.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/api/v1.php');
    }
}
