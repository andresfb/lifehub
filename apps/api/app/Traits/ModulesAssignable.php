<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use App\Services\Modules\ModuleAccessService;

trait ModulesAssignable
{
    private ?User $admin = null;

    private ?ModuleAccessService $service = null;

    private function getAdmin(): User
    {
        if (filled($this->admin)) {
            return $this->admin;
        }

        $this->admin = User::query()
            ->role(ModuleAccessService::SUPER_ADMIN_ROLE)
            ->firstOrFail();

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
        $this->getService()->grantPublicWriters($user);
    }

    private function assignModulesToAdmin(User $user): void
    {
        $this->getService()->grantSuperAdmin($user);
    }
}
