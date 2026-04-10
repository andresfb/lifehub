<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Console\Commands\Base\BaseUserCommand;
use Exception;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Route;
use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\warning;

final class TestAppCommand extends BaseUserCommand
{
    protected $signature = 'test:app {--user=}';

    protected $description = 'Command to run random tests';

    public function handle(): int
    {
        try {
            clear();
            intro('Running tests');
            Log::notice('Running tests');

            $user = $this->loadUser();

            $routes = Route::getRoutes();

            // TODO: app manifest
            foreach ($routes as $route) {
                dump([
                    'uri' => $route->uri(),
                    'methods' => $route->methods(),
                    'action' => $route->getActionName(),
                    'name' => $route->getName(),
                ]);
            }


            return self::SUCCESS;
        } catch (Exception $e) {
            warning($e->getTraceAsString());
            error($e->getMessage());
            Log::error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
            Log::notice('Done');
        }
    }
}
