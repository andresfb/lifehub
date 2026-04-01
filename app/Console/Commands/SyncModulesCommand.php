<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Modules\ModuleRegistry;
use Illuminate\Console\Command;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class SyncModulesCommand extends Command
{
    protected $signature = 'sync:modules';

    protected $description = 'Sync nwidart modules into the modules table';

    public function handle(ModuleRegistry $registry): int
    {
        try {
            clear();
            intro('Syncing modules');

            $registry->syncToDatabase();

            info('Modules synced successfully.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            error($e->getMessage());

            return self::FAILURE;
        } finally {
            $this->newLine();
            outro('Done');
        }
    }
}
