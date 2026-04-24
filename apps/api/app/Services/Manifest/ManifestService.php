<?php

declare(strict_types=1);

namespace App\Services\Manifest;

use App\Contracts\ManifestProviderInterface;
use App\Dtos\Manifest\ManifestItem;
use App\Dtos\Manifest\ModuleActionItem;
use App\Dtos\Manifest\ModuleCommandItem;
use App\Dtos\Manifest\ModuleManifest;
use App\Dtos\Manifest\NavigationItem;
use App\Enums\ModuleAccessLevel;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Spatie\ResponseCache\Facades\ResponseCache;

final readonly class ManifestService
{
    /** @var Collection<int, ManifestProviderInterface> */
    private Collection $providers;

    public function __construct(
        private EndpointResolver $endpointResolver,
        private ModuleAccessService $accessService,
    ) {
        $this->providers = collect();
    }

    public static function invalidateCache(): void
    {
        Cache::tags(['manifest'])->flush();

        ResponseCache::clear(['manifest']);
    }

    public function register(ManifestProviderInterface $provider): void
    {
        $this->providers->push($provider);
    }

    public function buildFull(): ManifestItem
    {
        $modules = $this->providers->map(fn (ManifestProviderInterface $provider): ModuleManifest => new ModuleManifest(
            key: $provider->moduleKey(),
            name: $provider->moduleName(),
            description: $provider->moduleDescription(),
            isPublic: $provider->isPublic(),
            navigation: $this->resolveNavigation($provider->navigation()),
            commands: $this->resolveCommands($provider->commands()),
            actions: $this->resolveActions($provider->actions()),
        ));

        return new ManifestItem(
            version: Config::string('settings.manifest.version'),
            modules: $modules,
        );
    }

    public function buildForUser(User $user): array
    {
        return Cache::tags(['manifest'])
            ->remember(
                md5("manifesto:user:{$user->id}"),
                now()->addWeek(),
                function () use ($user): array {
                    $full = $this->buildFull();

                    $filtered = $full->modules
                        ->filter(fn (ModuleManifest $module): bool => ($module->isPublic || $user->isAdmin())
                            && $this->accessService->canRead($user, $module->key->value))
                        ->map(function (ModuleManifest $module) use ($user): ModuleManifest {
                            $userLevel = $this->accessService->canWrite($user, $module->key->value)
                                ? ModuleAccessLevel::WRITE
                                : ModuleAccessLevel::READ;

                            return new ModuleManifest(
                                key: $module->key,
                                name: $module->name,
                                description: $module->description,
                                isPublic: $module->isPublic,
                                navigation: $module->navigation,
                                commands: $this->filterCommands($module->commands, $userLevel),
                                actions: $this->filterActions($module->actions, $userLevel),
                            );
                        })
                        ->values();

                    return new ManifestItem(
                        version: $full->version,
                        modules: $filtered,
                    )->toArray();
                });
    }

    private function resolveNavigation(NavigationItem $navigation): NavigationItem
    {
        return new NavigationItem(
            id: $navigation->id,
            key: $navigation->key,
            name: $navigation->name,
            webPath: $navigation->webPath,
            icon: $navigation->icon,
            shortcut: $navigation->shortcut,
            show: $navigation->show,
            nodes: $this->resolveNodes($navigation->nodes),
        );
    }

    /**
     * @param  Collection<int, NavigationItem>|null  $nodes
     * @return Collection<int, NavigationItem>|null
     */
    private function resolveNodes(?Collection $nodes): ?Collection
    {
        if (blank($nodes)) {
            return null;
        }

        return $nodes->map(fn (NavigationItem $item): NavigationItem => $this->resolveNavigation($item));
    }

    /**
     * @param  Collection<int, ModuleCommandItem>|null  $commands
     * @return Collection<int, ModuleCommandItem>|null
     */
    private function resolveCommands(?Collection $commands): ?Collection
    {
        if (blank($commands)) {
            return null;
        }

        return $commands->map(fn (ModuleCommandItem $item): ModuleCommandItem => $this->resolveCommand($item));
    }

    private function resolveCommand(ModuleCommandItem $item): ?ModuleCommandItem
    {
        if (blank($item)) {
            return null;
        }

        return new ModuleCommandItem(
            owner: $item->owner,
            code: $item->code,
            name: $item->name,
            requiredAccess: $item->requiredAccess,
            endpoint: $this->endpointResolver->resolve($item->endpoint),
            shortcut: $item->shortcut,
        );
    }

    /**
     * @param  Collection<int, ModuleActionItem>|null  $actions
     * @return Collection<int, ModuleActionItem>|null
     */
    private function resolveActions(?Collection $actions): ?Collection
    {
        if (blank($actions)) {
            return null;
        }

        return $actions->map(fn (ModuleActionItem $action): ModuleActionItem => $this->resolveAction($action));
    }

    private function resolveAction(?ModuleActionItem $action): ?ModuleActionItem
    {
        if (blank($action)) {
            return null;
        }

        return new ModuleActionItem(
            owner: $action->owner,
            name: $action->name,
            requiredAccess: $action->requiredAccess,
            endpoint: $this->endpointResolver->resolve($action->endpoint),
        );
    }

    /**
     * @param  Collection<int, ModuleCommandItem>|null  $commands
     * @return Collection<int, ModuleCommandItem>|null
     */
    private function filterCommands(?Collection $commands, ModuleAccessLevel $userLevel): ?Collection
    {
        if (blank($commands)) {
            return null;
        }

        return $commands
            ->filter(fn (ModuleCommandItem $item): bool => $userLevel->allows($item->requiredAccess))
            ->map(fn (ModuleCommandItem $item): ModuleCommandItem => $this->resolveCommand($item));
    }

    /**
     * @param  Collection<int, ModuleActionItem>|null  $actions
     * @return Collection<int, ModuleActionItem>|null
     */
    private function filterActions(?Collection $actions, ModuleAccessLevel $userLevel): ?Collection
    {
        if (blank($actions)) {
            return null;
        }

        return $actions
            ->filter(fn (ModuleActionItem $item): bool => $userLevel->allows($item->requiredAccess))
            ->map(fn (ModuleActionItem $item): ModuleActionItem => $this->resolveAction($item));
    }
}
