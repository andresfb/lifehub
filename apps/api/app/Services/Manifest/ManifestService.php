<?php

declare(strict_types=1);

namespace App\Services\Manifest;

use App\Contracts\ManifestProvider;
use App\Dtos\Manifest\FeatureAction;
use App\Dtos\Manifest\FeatureNode;
use App\Dtos\Manifest\ManifestItem;
use App\Dtos\Manifest\MenuItem;
use App\Dtos\Manifest\ModuleManifest;
use App\Enums\ModuleAccessLevel;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Spatie\ResponseCache\Facades\ResponseCache;

final readonly class ManifestService
{
    /** @var Collection<int, ManifestProvider> */
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

    public function register(ManifestProvider $provider): void
    {
        $this->providers->push($provider);
    }

    public function buildFull(): ManifestItem
    {
        $modules = $this->providers->map(fn (ManifestProvider $provider): ModuleManifest => new ModuleManifest(
            key: $provider->moduleKey(),
            name: $provider->moduleName(),
            description: $provider->moduleDescription(),
            isPublic: $provider->isPublic(),
            features: $this->resolveFeatures($provider->features()),
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
                        ->filter(fn(ModuleManifest $module): bool => ($module->isPublic || $user->isAdmin())
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
                                features: $this->filterFeatures($module->features, $userLevel),
                            );
                        })
                        ->values();

                    return new ManifestItem(
                        version: $full->version,
                        modules: $filtered,
                    )->toArray();
                });
    }

    /**
     * @param  Collection<int, FeatureNode>  $features
     * @return Collection<int, FeatureNode>
     */
    private function resolveFeatures(Collection $features): Collection
    {
        return $features->map(fn (FeatureNode $node): FeatureNode => $this->resolveFeature($node));
    }

    private function resolveFeature(FeatureNode $node): FeatureNode
    {
        return new FeatureNode(
            id: $node->id,
            title: $node->title,
            requiredAccess: $node->requiredAccess,
            menuItem: $this->resolveMenuItem($node->menuItem),
            nodes: $this->resolveNodes($node->nodes),
        );
    }

    private function resolveMenuItem(MenuItem $menuItem): MenuItem
    {
        return new MenuItem(
            name: $menuItem->name,
            webPath: $menuItem->webPath,
            icon: $menuItem->icon,
            shortcut: $menuItem->shortcut,
            show: $menuItem->show,
            actions: $this->resolveActions($menuItem->actions),
        );
    }

    /**
     * @param  Collection<int, FeatureAction>|null $actions
     * @return Collection<int, FeatureAction>|null
     */
    private function resolveActions(?Collection $actions): ?Collection
    {
        if (blank($actions)) {
            return null;
        }

        return $actions->map(fn (FeatureAction $action): FeatureAction => $this->resolveAction($action));
    }

    private function resolveAction(?FeatureAction $action): ?FeatureAction
    {
        if (blank($action)) {
            return null;
        }

        return new FeatureAction(
            name: $action->name,
            requiredAccess: $action->requiredAccess,
            endpoint: $this->endpointResolver->resolve($action->endpoint),
        );
    }

    /**
     * @param  Collection<int, MenuItem>|null $nodes
     * @return Collection<int, MenuItem>|null
     */
    private function resolveNodes(?Collection $nodes): ?Collection
    {
        if (blank($nodes)) {
            return null;
        }

        return $nodes->map(fn (MenuItem $item): MenuItem => $this->resolveMenuItem($item));
    }

    /**
     * @param  Collection<int, FeatureNode>  $features
     * @return Collection<int, FeatureNode>
     */
    private function filterFeatures(Collection $features, ModuleAccessLevel $userLevel): Collection
    {
        return $features
            ->filter(fn (FeatureNode $node): bool => $userLevel->allows($node->requiredAccess))
            ->map(function (FeatureNode $node) use ($userLevel): FeatureNode {
                $feature = $this->resolveFeature($node);

                if (blank($feature->menuItem->actions)) {
                    return $feature;
                }

                return $feature->menuItem
                    ->actions
                    ->filter(fn (FeatureAction $action): bool => $userLevel->allows($action->requiredAccess))
                    ->map(fn (FeatureAction $action): FeatureAction => $this->resolveAction($action));
            })
            ->values();
    }
}
