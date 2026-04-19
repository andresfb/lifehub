<?php

declare(strict_types=1);

namespace Modules\Core\Manifest;

use App\Contracts\ManifestProvider;
use App\Dtos\Manifest\EndpointBinding;
use App\Dtos\Manifest\FeatureNode;
use App\Dtos\Manifest\NavHints;
use App\Enums\FeatureKind;
use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;

final readonly class CoreManifestProvider implements ManifestProvider
{
    public function __construct(
        private ModuleAccessService $accessService
    ) {}

    public function moduleKey(): ModuleKey
    {
        return ModuleKey::CORE;
    }

    public function moduleName(): string
    {
        return str(ModuleKey::CORE->value)
            ->title()
            ->toString();
    }

    public function moduleDescription(): string
    {
        return 'Core Functionality';
    }

    public function isPublic(): bool
    {
        $module = Module::find(ModuleKey::CORE->value);

        return $this->accessService->isPublic($module);
    }

    /**
     * {@inheritDoc}
     */
    public function features(): Collection
    {
        return collect([
            new FeatureNode(
                id: 'ai.providers',
                title: 'AI Providers',
                kind: FeatureKind::Group,
                requiredAccess: ModuleAccessLevel::READ,
                nav: new NavHints(webPath: '/ai/providers', icon: 'ai'),
                children: collect([
                    new FeatureNode(
                        id: 'ai.providers.list',
                        title: 'View AI Providers',
                        kind: FeatureKind::Screen,
                        requiredAccess: ModuleAccessLevel::READ,
                        endpoints: collect([
                            new EndpointBinding(routeName: 'v1.ai.providers.index'),
                            new EndpointBinding(routeName: 'v1.ai.providers.show'),
                        ]),
                    ),
                    new FeatureNode(
                        id: 'ai.providers.manage',
                        title: 'Manage AI Providers',
                        kind: FeatureKind::Action,
                        requiredAccess: ModuleAccessLevel::WRITE,
                        endpoints: collect([
                            new EndpointBinding(routeName: 'v1.ai.providers.store'),
                            new EndpointBinding(routeName: 'v1.ai.providers.update'),
                            new EndpointBinding(routeName: 'v1.ai.providers.destroy'),
                        ]),
                    ),
                ]),
            ),
            new FeatureNode(
                id: 'ai.models',
                title: 'AI Models',
                kind: FeatureKind::Group,
                requiredAccess: ModuleAccessLevel::READ,
                nav: new NavHints(webPath: '/ai/models', icon: 'ai'),
                children: collect([
                    new FeatureNode(
                        id: 'ai.models.list',
                        title: 'View AI Models',
                        kind: FeatureKind::Screen,
                        requiredAccess: ModuleAccessLevel::READ,
                        endpoints: collect([
                            new EndpointBinding(routeName: 'v1.ai.models.index'),
                            new EndpointBinding(routeName: 'v1.ai.models.show'),
                        ]),
                    ),
                    new FeatureNode(
                        id: 'ai.models.manage',
                        title: 'Manage AI Models',
                        kind: FeatureKind::Action,
                        requiredAccess: ModuleAccessLevel::WRITE,
                        endpoints: collect([
                            new EndpointBinding(routeName: 'v1.ai.models.store'),
                            new EndpointBinding(routeName: 'v1.ai.models.update'),
                            new EndpointBinding(routeName: 'v1.ai.models.destroy'),
                        ]),
                    ),
                ]),
            ),
        ]);
    }
}
