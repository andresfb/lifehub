<?php

declare(strict_types=1);

namespace App\Traits;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleStatus;
use App\Models\AppModule;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

trait ModulesAssignable
{
    private User $admin;

    private ModuleAccessService $service;

    private function getAdmin(): User
    {
        if (filled($this->admin)) {
            return $this->admin;
        }

        $this->admin = User::query()
            ->whereNotNull('admin_hash')
            ->firstOrFail();

        if (! $this->admin->isAdmin()) {
            throw new AccessDeniedHttpException("User: {$this->admin->name} is not an admin");
        }

        return $this->admin;
    }

    private function getService(): ModuleAccessService
    {
        if (filled($this->service)) {
            return $this->service;
        }

        $this->service = resolve(ModuleAccessService::class);

        return $this->service;
    }

    private function assignDefaultModules(User $user): void
    {
        $modules = AppModule::query()
            ->where('status', ModuleStatus::ACTIVE)
            ->where('is_public', true)
            ->get();

        $modules->each(function (AppModule $module) use ($user) {
            $this->getService()->grant(
                targetUser: $user,
                module: $module,
                grantedBy: $this->getAdmin(),
            );
        });
    }

    private function assignModulesToAdmin(User $user): void
    {
        AppModule::query()->each(function (AppModule $module) use ($user) {
            $this->getService()->grant(
                targetUser: $user,
                module: $module,
                grantedBy: $user,
                accessLevel: ModuleAccessLevel::ADMIN,
            );
        });
    }
}
