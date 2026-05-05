<?php

declare(strict_types=1);

namespace Modules\Core\Manifest;

use App\Contracts\ManifestProviderInterface;
use App\Dtos\Manifest\EndpointBinding;
use App\Dtos\Manifest\ModuleActionItem;
use App\Dtos\Manifest\ModuleCommandItem;
use App\Dtos\Manifest\NavigationItem;
use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleEndpointType;
use App\Enums\ModuleKey;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;

final readonly class CoreManifestProvider implements ManifestProviderInterface
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

    public function navigation(): NavigationItem
    {
        return new NavigationItem(
            id: 'ai',
            key: $this->moduleKey(),
            name: 'AI Information',
            webPath: '/ai',
            icon: 'ai',
            show: false,
            nodes: collect([
                new NavigationItem(
                    id: 'ai.providers',
                    key: $this->moduleKey(),
                    name: 'AI Providers',
                    webPath: '/ai/providers',
                    icon: 'ai',
                    show: false,
                ),
                new NavigationItem(
                    id: 'ai.modles',
                    key: $this->moduleKey(),
                    name: 'AI Models',
                    webPath: '/ai/models',
                    icon: 'ai',
                    show: false,
                ),
            ]),
        );
    }

    /**
     * {inheritdoc}
     */
    public function commands(): Collection
    {
        return collect([
            new ModuleCommandItem(
                owner: 'global.search',
                code: 'glob',
                name: 'Global Search',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(
                    routeName: 'v1.search',
                    type: ModuleEndpointType::COMMAND,
                ),
                shortcut: 'CTR+ALT+G',
            ),
        ]);
    }

    /**
     * {inheritdoc}
     */
    public function actions(): Collection
    {
        return collect([
            new ModuleActionItem(
                owner: 'global.search',
                name: 'search',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.search'),
            ),
            new ModuleActionItem(
                owner: 'search.tags',
                name: 'search',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.search.tags'),
            ),
            new ModuleActionItem(
                owner: 'ai.providers',
                name: 'list',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.index'),
            ),
            new ModuleActionItem(
                owner: 'ai.providers',
                name: 'show',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.show'),
            ),
            new ModuleActionItem(
                owner: 'ai.providers',
                name: 'save',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.store'),
            ),
            new ModuleActionItem(
                owner: 'ai.providers',
                name: 'update',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.update'),
            ),
            new ModuleActionItem(
                owner: 'ai.providers',
                name: 'delete',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.providers.destroy'),
            ),
            new ModuleActionItem(
                owner: 'ai.models',
                name: 'list',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.ai.models.index'),
            ),
            new ModuleActionItem(
                owner: 'ai.models',
                name: 'show',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.ai.models.show'),
            ),
            new ModuleActionItem(
                owner: 'ai.models',
                name: 'save',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.models.store'),
            ),
            new ModuleActionItem(
                owner: 'ai.models',
                name: 'update',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.models.update'),
            ),
            new ModuleActionItem(
                owner: 'ai.models',
                name: 'delete',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.ai.models.destroy'),
            ),
        ]);
    }
}
