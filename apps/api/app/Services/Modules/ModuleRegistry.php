<?php

declare(strict_types=1);

namespace App\Services\Modules;

use App\Models\Module;
use Illuminate\Support\Facades\Cache;

final readonly class ModuleRegistry
{
    public function __construct(
        private ModuleAccessService $moduleAccess
    ) {}

    public function syncToDatabase(array $records): void
    {
        if (blank($records)) {
            return;
        }

        Module::query()
            ->upsert(
                $records,
                ['key'],
                ['name', 'description', 'is_core', 'is_public', 'status']
            );

        Cache::tags('users')->flush();
    }

    public function syncAndAssign(array $records): void
    {
        $this->syncToDatabase($records);
        $this->syncPermissions();
    }

    public function syncPermissions(): void
    {
        $this->moduleAccess->syncPermissions();
    }
}
