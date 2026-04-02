<?php

declare(strict_types=1);

namespace App\Services\Modules;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Enums\ModuleStatus;
use App\Enums\ModuleVisibility;
use App\Models\Module;
use App\Models\User;

final class ModuleAccessService
{
    public function canUse(
        User $user,
        ModuleKey $moduleKey,
        ModuleAccessLevel $requiredLevel = ModuleAccessLevel::READ
    ): bool {
        $module = Module::query()
            ->where('key', $moduleKey)
            ->first();

        if (blank($module)) {
            return false;
        }

        if ($module->status !== ModuleStatus::ACTIVE) {
            return false;
        }

        $userModule = $user->userModules()
            ->where('module_id', $module->id)
            ->where('enabled', true)
            ->first();

        if (! $userModule) {
            return false;
        }

        return $userModule->access_level->allows($requiredLevel);
    }

    public function grant(
        User $targetUser,
        Module $module,
        bool $enabled = true,
        ?User $grantedBy = null,
        ?array $settings = null,
        ModuleAccessLevel $accessLevel = ModuleAccessLevel::READ,
        ModuleVisibility $visibility = ModuleVisibility::VISIBLE,
    ): void {
        $targetUser->userModules()->updateOrCreate(
            ['module_id' => $module->id],
            [
                'enabled' => $enabled,
                'access_level' => $accessLevel, // enum auto-cast
                'visibility' => $visibility,
                'settings' => $settings,
                'granted_by' => $grantedBy?->id,
            ]
        );
    }
}
