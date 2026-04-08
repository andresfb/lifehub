<?php

declare(strict_types=1);

namespace App\Domain\Core\Providers;

use App\Domain\Core\Enums\MorphTypes;
use App\Dtos\Modules\ModuleRecordItem;
use App\Dtos\Modules\MorphTypesItems;
use App\Enums\ModuleStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

// TODO: move the Homepage and Search Providers to a separate Module

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('morph_types', function (Collection $types): void {
            $types->add(
                new MorphTypesItems(
                    MorphTypes::CORE_REMINDER->name,
                    MorphTypes::CORE_REMINDER->value,
                ),
            );
            $types->add(
                new MorphTypesItems(
                    MorphTypes::CORE_HOMEPAGE_ITEM->name,
                    MorphTypes::CORE_HOMEPAGE_ITEM->value,
                ),
            );
        });

        $this->app->resolving('module_records', function (Collection $records): void {
            $records->add(
                new ModuleRecordItem(
                    key: 'CORE',
                    name: 'Core Module',
                    description: 'The base Module where all starts',
                    is_core: true,
                    is_public: true,
                    status: ModuleStatus::ACTIVE,
                )
            );
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        Route::middleware('web')
            ->group(__DIR__.'/../Routes/web.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/api/v1.php');
    }
}
