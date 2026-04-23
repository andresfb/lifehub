<?php

declare(strict_types=1);

namespace Modules\Core\Manifest;

use App\Contracts\ManifestProvider;
use App\Dtos\Manifest\EndpointBinding;
use App\Dtos\Manifest\FeatureAction;
use App\Dtos\Manifest\FeatureNode;
use App\Dtos\Manifest\MenuItem;
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
                id: 'ai',
                title: 'AI',
                requiredAccess: ModuleAccessLevel::READ,
                menuItem: new MenuItem(
                    name: 'AI Information',
                    webPath: '/ai',
                    icon: 'ai',
                    show: false,
                ),
                nodes: collect([
                    new MenuItem(
                        name: 'AI Providers',
                        webPath: '/ai/providers',
                        icon: 'ai',
                        show: false,
                        actions: collect([
                            new FeatureAction(
                                name: 'list',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.index'),
                            ),
                            new FeatureAction(
                                name: 'show',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.show'),
                            ),
                            new FeatureAction(
                                name: 'save',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.store'),
                            ),
                            new FeatureAction(
                                name: 'update',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.update'),
                            ),
                            new FeatureAction(
                                name: 'delete',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.destroy'),
                            ),
                        ]),
                    ),
                    new MenuItem(
                        name: 'AI Models',
                        webPath: '/ai/models',
                        icon: 'ai',
                        show: false,
                        actions: collect([
                            new FeatureAction(
                                name: 'list',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.models.index'),
                            ),
                            new FeatureAction(
                                name: 'show',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.models.show'),
                            ),
                            new FeatureAction(
                                name: 'save',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.models.store'),
                            ),
                            new FeatureAction(
                                name: 'update',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.models.update'),
                            ),
                            new FeatureAction(
                                name: 'delete',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.ai.models.destroy'),
                            ),
                        ]),
                    ),
                ]),
            ),
        ]);
    }
}
