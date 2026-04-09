<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module}';

    protected $description = 'Scaffold a new domain module under app/Domain';

    public function handle(): int
    {
        try {
            clear();
            intro('Creating new module');

            $name = (string) $this->argument('name');
            $pascalName = Str::studly($name);
            $upperName = Str::upper(Str::snake($name));
            $lowerName = Str::lower($upperName);
            $basePath = app_path("Domain/{$pascalName}");

            if (File::isDirectory($basePath)) {
                error("Module '{$pascalName}' already exists at app/Domain/{$pascalName}");

                return self::FAILURE;
            }

            $directories = [
                'Actions',
                'Commands',
                'Configs',
                'Database/Factories',
                'Database/Migrations',
                'Database/Seeders',
                'Dtos',
                'Enums',
                'Http/Controllers',
                'Http/Requests',
                'Jobs',
                'Libraries',
                'Models',
                'Policies',
                'Observers',
                'Providers',
                'Routes/api',
                'Services',
                'Traits',
            ];

            foreach ($directories as $dir) {
                File::makeDirectory("{$basePath}/{$dir}", 0755, true);
            }

            File::put("{$basePath}/Enums/MorphTypes.php", $this->morphTypesStub($pascalName, $upperName));
            File::put("{$basePath}/Providers/{$pascalName}ServiceProvider.php", $this->serviceProviderStub($pascalName, $upperName, $lowerName));
            File::put("{$basePath}/Database/Seeders/{$pascalName}Seeder.php", $this->seederStub($pascalName, $upperName));
            File::put("{$basePath}/Routes/web.php", $this->webRouteStub($lowerName));
            File::put("{$basePath}/Routes/api/v1.php", $this->apiRouteStub($lowerName));
            File::put("{$basePath}/Configs/config.php", $this->configStub());

            collect(File::allDirectories($basePath))
                ->filter(fn (string $dir): bool => count(File::files($dir)) === 0 && count(File::directories($dir)) === 0)
                ->each(fn (string $dir) => File::put("{$dir}/.gitkeep", ''));

            $providersPath = base_path('bootstrap/providers.php');
            $providerClass = "App\\Domain\\{$pascalName}\\Providers\\{$pascalName}ServiceProvider::class";
            $providersContent = File::get($providersPath);
            $providersContent = str_replace(
                '];',
                "    {$providerClass},\n];",
                $providersContent,
            );
            File::put($providersPath, $providersContent);

            $seederPath = base_path('database/seeders/DatabaseSeeder.php');
            $seederClass = "App\\Domain\\{$pascalName}\\Database\\Seeders\\{$pascalName}Seeder::class";
            $seederContent = File::get($seederPath);
            $seederContent = str_replace(
                ']);',
                "    {$seederClass},\n]);",
                $seederContent,
            );
            File::put($seederPath, $seederContent);

            $moduleKeyPath = app_path('Enums/ModuleKey.php');
            $moduleKeyContent = File::get($moduleKeyPath);
            $moduleKeyContent = str_replace(
                '}',
                "    case {$upperName} = '{$lowerName}';\n}",
                $moduleKeyContent,
            );
            File::put($moduleKeyPath, $moduleKeyContent);

            info("Module '{$pascalName}' created successfully at app/Domain/{$pascalName}");

            return self::SUCCESS;
        } catch (Throwable $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }

    private function morphTypesStub(string $name, string $upperName): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Domain\\{$name}\Enums;

        enum MorphTypes: string
        {
            case {$upperName}_PLACEHOLDER = '{$name}_place_holder';
        }
        PHP;
    }

    private function serviceProviderStub(string $name, string $upperName, string $lowerName): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Domain\\{$name}\Providers;

        use App\Dtos\Modules\ModuleRecordItem;
        use App\Dtos\Modules\MorphTypesItems;
        use App\Enums\ModuleKey;
        use App\Enums\ModuleStatus;
        use Illuminate\Contracts\Container\BindingResolutionException;
        use Illuminate\Database\Eloquent\Factories\Factory;
        use Illuminate\Support\Facades\Route;
        use Illuminate\Support\ServiceProvider;
        use Illuminate\Support\Collection;
        use App\Domain\{$name}\Enums\MorphTypes;

        final class {$name}ServiceProvider extends ServiceProvider
        {
            public function register(): void
            {
                \$this->app->resolving('morph_types', function (Collection \$types): void {
                    \$types->add(
                        new MorphTypesItems(
                            MorphTypes::{$upperName}_PLACEHOLDER->name,
                            MorphTypes::{$upperName}_PLACEHOLDER->value,
                        )
                    );
                });

                \$this->app->resolving('module_records', function (Collection \$records): void {
                    \$records->add(
                        new ModuleRecordItem(
                            key: ModuleKey.{$upperName},
                            name: '{$name}',
                            description: 'placeholder description',
                            is_core: false,
                            is_public: false,
                            status: ModuleStatus::ACTIVE,
                            show_menu: true,
                        )
                    );
                });
            }

            /**
             * @throws BindingResolutionException
             */
            public function boot(): void
            {
                \$this->mergeConfigFrom(__DIR__.'/../Configs/config.php', '{$lowerName}');

                Route::middleware('web')->group(__DIR__.'/../Routes/web.php');
                \$this->loadRoutesFrom(__DIR__.'/../Routes/api/v1.php');

                \$this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');
            }
        }
        PHP;
    }

    private function seederStub(string $name, string $upperName): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        namespace App\Domain\\{$name}\Database\Seeders;

        use App\Services\Modules\ModuleRegistry;
        use Illuminate\Database\Seeder;
        use Illuminate\Support\Collection;
        use RuntimeException;

        final class {$name}Seeder extends Seeder
        {
            public function __construct(
                private readonly ModuleRegistry \$registry,
            ) {}

            public function run(): void
            {
                \$modules = resolve('module_records');
                if (! \$modules instanceof Collection) {
                    throw new RuntimeException('Modules Records not found');
                }

                if (\$modules->isEmpty()) {
                    throw new RuntimeException('Modules Records not found');
                }

                \$records = \$modules->where('key', '{$upperName}')->firstOrFail();

                \$this->registry->syncAndAssign(\$records->toArray());
            }
        }
        PHP;
    }

    private function webRouteStub(string $lowerName): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        use Illuminate\Support\Facades\Route;

        Route::middleware([
            'auth',
            'verified',
            'module.access:{$lowerName},read',
        ])
            ->prefix('{$lowerName}')
            ->group(function () {

            });
        PHP;
    }

    private function apiRouteStub(string $lowerName): string
    {
        return <<<PHP
        <?php

        declare(strict_types=1);

        use Illuminate\Support\Facades\Route;

        Route::middleware([
            'auth:sanctum',
            'throttle:authenticated',
            'module.access:{$lowerName},read',
        ])
            ->prefix('api/v1/{$lowerName}')
            ->group(function (): void {

            });
        PHP;
    }

    private function configStub(): string
    {
        return <<<'PHP'
        <?php

        declare(strict_types=1);
        /** @noinspection LaravelFunctionsInspection */

        return [

        ];
        PHP;
    }
}
