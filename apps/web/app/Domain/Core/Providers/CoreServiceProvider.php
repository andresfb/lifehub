<?php

declare(strict_types=1);

namespace App\Domain\Core\Providers;

use App\Dtos\ModuleRecordItem;
use App\Dtos\MorphTypesItems;
use App\Enums\ModuleStatus;
use App\Enums\MorphTypes;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving('morph_types', function (Collection $types): void {
            $types->add(
                new MorphTypesItems(
                    MorphTypes::CORE_REMINDER->name,
                    MorphTypes::CORE_REMINDER->value,
                )
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

    public function boot(): void {}
}
