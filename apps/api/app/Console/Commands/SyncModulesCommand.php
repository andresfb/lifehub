<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Console\Command;
use Throwable;

use function Laravel\Prompts\clear;
use function Laravel\Prompts\error;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;

final class SyncModulesCommand extends Command
{
    protected $signature = 'sync:modules';

    protected $description = 'Sync enabled nwidart modules into Spatie roles and permissions';

    public function handle(ModuleAccessService $moduleAccess): int
    {
        try {
            clear();
            intro('Syncing modules');

            $moduleAccess->syncPermissions();

            User::query()
                ->role(ModuleAccessService::SUPER_ADMIN_ROLE)
                ->each(fn (User $user): null => $moduleAccess->grantAllModuleWriters($user));

            $this->components->info('Modules synced successfully.');

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
