<?php

declare(strict_types=1);

namespace App\Services\Modules;

use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module;
use Nwidart\Modules\Module as NwidartModule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class ModuleAccessService
{
    public const string SUPER_ADMIN_ROLE = 'super-admin';

    private const string READ_ACTION = 'read';

    private const string WRITE_ACTION = 'write';

    public function syncPermissions(): void
    {
        $this->enabledModuleKeys()
            ->each(function (string $moduleKey): void {
                $readPermission = Permission::findOrCreate(
                    $this->permissionName($moduleKey, self::READ_ACTION),
                    $this->guardName(),
                );

                $writePermission = Permission::findOrCreate(
                    $this->permissionName($moduleKey, self::WRITE_ACTION),
                    $this->guardName(),
                );

                Role::findOrCreate($this->readerRoleName($moduleKey), $this->guardName())
                    ->syncPermissions([$readPermission]);

                Role::findOrCreate($this->writerRoleName($moduleKey), $this->guardName())
                    ->syncPermissions([$readPermission, $writePermission]);
            });

        Role::findOrCreate(self::SUPER_ADMIN_ROLE, $this->guardName());

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function grantReader(User $user, string $moduleKey): void
    {
        $this->syncPermissions();

        $this->assignReader($user, $moduleKey);
    }

    public function grantWriter(User $user, string $moduleKey): void
    {
        $this->syncPermissions();

        $this->assignWriter($user, $moduleKey);
    }

    public function grantPublicWriters(User $user): void
    {
        $this->syncPermissions();

        $this->publicModuleKeys()
            ->each(fn (string $moduleKey): User => $this->assignWriter($user, $moduleKey));
    }

    public function grantAllModuleWriters(User $user): void
    {
        $this->syncPermissions();

        $this->enabledModuleKeys()
            ->each(fn (string $moduleKey): User => $this->assignWriter($user, $moduleKey));
    }

    public function grantSuperAdmin(User $user): void
    {
        $this->grantAllModuleWriters($user);

        $user->assignRole(self::SUPER_ADMIN_ROLE);
    }

    public function revokeModule(User $user, string $moduleKey): void
    {
        $user->removeRole($this->readerRoleName($moduleKey));
        $user->removeRole($this->writerRoleName($moduleKey));
    }

    /**
     * @return Collection<int, string>
     */
    public function modulesFor(User $user): Collection
    {
        return $this->enabledModuleKeys()
            ->filter(fn (string $moduleKey): bool => $this->canRead($user, $moduleKey))
            ->values();
    }

    public function canRead(User $user, string $moduleKey): bool
    {
        return $user->can($this->permissionName($moduleKey, self::READ_ACTION));
    }

    public function canWrite(User $user, string $moduleKey): bool
    {
        return $user->can($this->permissionName($moduleKey, self::WRITE_ACTION));
    }

    public function canUse(
        User $user,
        ModuleKey $moduleKey,
        ModuleAccessLevel $requiredLevel = ModuleAccessLevel::READ
    ): bool {
        if ($requiredLevel === ModuleAccessLevel::READ) {
            return $this->canRead($user, $moduleKey->value);
        }

        return $this->canWrite($user, $moduleKey->value);
    }

    public function permissionName(string $moduleKey, string $action): string
    {
        return "module.{$this->normalizeModuleKey($moduleKey)}.{$action}";
    }

    public function readerRoleName(string $moduleKey): string
    {
        return "module.{$this->normalizeModuleKey($moduleKey)}.reader";
    }

    public function writerRoleName(string $moduleKey): string
    {
        return "module.{$this->normalizeModuleKey($moduleKey)}.writer";
    }

    /**
     * @return Collection<int, string>
     */
    public function enabledModuleKeys(): Collection
    {
        return collect(Module::allEnabled())
            ->map(fn (NwidartModule $module): string => $this->moduleKey($module))
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    public function publicModuleKeys(): Collection
    {
        return collect(Module::allEnabled())
            ->filter(fn (NwidartModule $module): bool => $this->isPublic($module))
            ->map(fn (NwidartModule $module): string => $this->moduleKey($module))
            ->values();
    }

    private function assignReader(User $user, string $moduleKey): User
    {
        $user->removeRole($this->writerRoleName($moduleKey));

        return $user->assignRole($this->readerRoleName($moduleKey));
    }

    private function assignWriter(User $user, string $moduleKey): User
    {
        $user->removeRole($this->readerRoleName($moduleKey));

        return $user->assignRole($this->writerRoleName($moduleKey));
    }

    private function isPublic(NwidartModule $module): bool
    {
        $access = $module->get('access', []);

        if (blank($access)) {
            return false;
        }

        return ($access['is_public'] ?? false) === true;
    }

    private function moduleKey(NwidartModule $module): string
    {
        return $this->normalizeModuleKey(
            (string) $module->get('alias', $module->getLowerName())
        );
    }

    private function normalizeModuleKey(string $moduleKey): string
    {
        return Str::of($moduleKey)
            ->lower()
            ->kebab()
            ->toString();
    }

    private function guardName(): string
    {
        return config('auth.defaults.guard', 'web');
    }
}
