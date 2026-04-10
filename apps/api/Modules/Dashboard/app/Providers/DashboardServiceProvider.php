<?php

namespace Modules\Dashboard\Providers;

use App\Dtos\Modules\MorphTypesItems;
use Illuminate\Support\Collection;
use Modules\Dashboard\Enums\MorphTypes;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Override;

class DashboardServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Dashboard';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'dashboard';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Define module schedules.
     *
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

    #[Override]
    public function register(): void
    {
        parent::register();

        $this->app->resolving('morph_types', function (Collection $types): void {
            $types->add(
                new MorphTypesItems(
                    MorphTypes::DASHBOARD_HOMEPAGE_ITEM->name,
                    MorphTypes::DASHBOARD_HOMEPAGE_ITEM->value,
                ),
            );
        });
    }
}
