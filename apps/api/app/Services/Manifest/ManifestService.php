<?php

declare(strict_types=1);

namespace App\Services\Manifest;

use App\Contracts\ManifestProvider;
use App\Dtos\Manifest\EndpointBinding;
use App\Dtos\Manifest\FeatureNode;
use App\Dtos\Manifest\ManifestItem;
use App\Dtos\Manifest\ModuleManifest;
use App\Enums\ModuleAccessLevel;
use App\Models\User;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
        $cached = Cache::tags(['manifest'])
            ->remember(md5('manifesto:full'), now()->addWeek(), function (): array {
                $modules = $this->providers->map(fn (ManifestProvider $provider): ModuleManifest => new ModuleManifest(
                    key: $provider->moduleKey(),
                    name: $provider->moduleName(),
                    description: $provider->moduleDescription(),
                    isPublic: $provider->isPublic(),
                    features: $this->resolveEndpoints($provider->features()),
                ));

                return new ManifestItem(
                    version: config('app.version', '1.0.0'),
                    modules: $modules,
                )->toArray();
            });

        return ManifestItem::from($cached);
    }

    public function buildForUser(User $user): ManifestItem
    {
        $full = $this->buildFull();

        $filtered = $full->modules
            ->filter(fn (ModuleManifest $module): bool => $this->accessService->canRead($user, $module->key->value))
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
        );
    }

    /**
     * @param  Collection<int, FeatureNode>  $features
     * @return Collection<int, FeatureNode>
     */
    private function resolveEndpoints(Collection $features): Collection
    {
        return $features->map(fn (FeatureNode $node): FeatureNode => new FeatureNode(
            id: $node->id,
            title: $node->title,
            kind: $node->kind,
            requiredAccess: $node->requiredAccess,
            nav: $node->nav,
            endpoints: $node->endpoints?->map(
                fn (EndpointBinding $ep): EndpointBinding => $this->endpointResolver->resolve($ep)
            ),
            children: $node->children instanceof Collection ? $this->resolveEndpoints($node->children) : null,
        ));
    }

    /**
     * @param  Collection<int, FeatureNode>  $features
     * @return Collection<int, FeatureNode>
     */
    private function filterFeatures(Collection $features, ModuleAccessLevel $userLevel): Collection
    {
        return $features
            ->filter(fn (FeatureNode $node): bool => $userLevel->allows($node->requiredAccess))
            ->map(fn (FeatureNode $node): FeatureNode => new FeatureNode(
                id: $node->id,
                title: $node->title,
                kind: $node->kind,
                requiredAccess: $node->requiredAccess,
                nav: $node->nav,
                endpoints: $node->endpoints,
                children: $node->children instanceof Collection
                    ? $this->filterFeatures($node->children, $userLevel)
                    : null,
            ))
            ->values();
    }
}
