<?php

declare(strict_types=1);

namespace App\Domain\Dashboard\Providers;

use App\Domain\Dashboard\Enums\MorphTypes;
use App\Dtos\Modules\MenuItem;
use App\Dtos\Modules\ModuleRecordItem;
use App\Dtos\Modules\MorphTypesItems;
use App\Enums\ModuleKey;
use App\Enums\ModuleStatus;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

final class DashboardServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('morph_types', function (Collection $types): void {
            $types->add(
                new MorphTypesItems(
                    MorphTypes::DASHBOARD_HOMEPAGE_ITEM->name,
                    MorphTypes::DASHBOARD_HOMEPAGE_ITEM->value,
                )
            );
        });

        $this->app->resolving('module_records', function (Collection $records): void {
            $records->add(
                new ModuleRecordItem(
                    key: ModuleKey::DASHBOARD,
                    name: 'Dashboard',
                    description: 'Application Entry Point',
                    isCore: false,
                    isPublic: true,
                    status: ModuleStatus::ACTIVE,
                    showMenu: true,
                    menu: new MenuItem(
                        code: 'DSB',
                        title: 'Dashboard',
                        routes: [
                            'web' => 'dashboard',
                            'api' => 'api.v1.dashboard',
                        ],
                        icon: 'home',
                    ),
                )
            );
        });
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Configs/config.php', 'dashboard');

        Route::middleware('web')->group(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api/v1.php');

        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
    }
}
