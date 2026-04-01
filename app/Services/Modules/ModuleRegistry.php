<?php

declare(strict_types=1);

namespace App\Services\Modules;

use App\Enums\ModuleStatus;
use App\Models\AppModule;
use App\Models\User;
use App\Traits\ModulesAssignable;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module as NwidartModule;
use Nwidart\Modules\Module;

final class ModuleRegistry
{
    use ModulesAssignable;

    public function discovered(): Collection
    {
        return collect(NwidartModule::allEnabled())
            ->map(function (Module $nwidartModule): ?array {
                $config = config("{$nwidartModule->getLowerName()}.lifehub_module");

                if (! is_array($config) || empty($config['key'])) {
                    return null;
                }

                return [
                    'key' => $config['key'],
                    'name' => $config['name'] ?? $nwidartModule->getName(),
                    'description' => $config['description'] ?? null,
                    'is_core' => (bool) ($config['is_core'] ?? false),
                    'is_public' => (bool) ($config['is_public'] ?? true),
                    'status' => $config['status'] ?? ModuleStatus::ACTIVE,
                ];
            })
            ->filter()
            ->values();
    }

    public function syncToDatabase(): void
    {
        $records = $this->discovered()->all();

        if (empty($records)) {
            return;
        }

        AppModule::query()->upsert(
            $records,
            ['key'],
            ['name', 'description', 'is_core', 'is_public', 'status']
        );

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
