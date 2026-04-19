<?php

declare(strict_types=1);

namespace Modules\Core\Providers;

use App\Dtos\Modules\MorphTypesItems;
use App\Services\Manifest\ManifestService;
use Exception;
use Illuminate\Support\Collection;
use Modules\Core\Enums\MorphTypes;
use Modules\Core\Manifest\CoreManifestProvider;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Override;

final class CoreServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Core';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'core';

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
     * @param  $schedule
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
                    MorphTypes::CORE_REMINDER->name,
                    MorphTypes::CORE_REMINDER->value,
                ),
            );
        });
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function boot(): void
    {
        parent::boot();

        $this->app->make(ManifestService::class)
            ->register(resolve(CoreManifestProvider::class));
    }
}
