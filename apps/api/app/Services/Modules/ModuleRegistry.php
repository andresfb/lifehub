<?php

declare(strict_types=1);

namespace App\Services\Modules;

use App\Models\Module;
use App\Models\User;
use App\Traits\ModulesAssignable;
use Illuminate\Support\Facades\Cache;

final class ModuleRegistry
{
    use ModulesAssignable;

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
        $this->assign();
    }

    public function assign(): void
    {
        $this->assignModulesToUsers();
        $this->assignModulesToAdmin($this->getAdmin());
    }

    private function assignModulesToUsers(): void
    {
        User::query()
            ->each(function (User $user) {
                $this->assignDefaultModules($user);
            });
    }
}
