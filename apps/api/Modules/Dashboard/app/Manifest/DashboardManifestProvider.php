<?php

declare(strict_types=1);

namespace Modules\Dashboard\Manifest;

use App\Contracts\ManifestProviderInterface;
use App\Dtos\Manifest\EndpointBinding;
use App\Dtos\Manifest\ModuleActionItem;
use App\Dtos\Manifest\ModuleCommandItem;
use App\Dtos\Manifest\NavigationItem;
use App\Enums\ModuleAccessLevel;
use App\Enums\ModuleKey;
use App\Services\Modules\ModuleAccessService;
use Illuminate\Support\Collection;
use Nwidart\Modules\Facades\Module;

final readonly class DashboardManifestProvider implements ManifestProviderInterface
{
    public function __construct(
        private ModuleAccessService $accessService
    ) {}

    public function moduleKey(): ModuleKey
    {
        return ModuleKey::DASHBOARD;
    }

    public function moduleName(): string
    {
        return str(ModuleKey::DASHBOARD->value)
            ->title()
            ->toString();
    }

    public function moduleDescription(): string
    {
        return 'Dashboard';
    }

    public function isPublic(): bool
    {
        $module = Module::find(ModuleKey::DASHBOARD->value);

        return $this->accessService->isPublic($module);
    }

    public function navigation(): NavigationItem
    {
        return new NavigationItem(
            id: 'dashboard',
            key: $this->moduleKey(),
            name: 'Dashboard',
            webPath: '/dashboard',
            icon: 'home',
            show: true,
            nodes: collect([
                new NavigationItem(
                    id: 'dashboard.pins',
                    key: $this->moduleKey(),
                    name: 'Pins',
                    webPath: '/dashboard/pins',
                    icon: 'pin',
                    show: true,
                ),
                new NavigationItem(
                    id: 'dashboard.search',
                    key: $this->moduleKey(),
                    name: 'Search Providers',
                    webPath: '/dashboard/search/providers',
                    icon: 'search',
                    show: true,
                ),
            ]),
        );
    }

    /**
     * {inheritdoc}
     */
    public function commands(): ?Collection
    {
        return collect([
            new ModuleCommandItem(
                owner: 'dashboard.pins',
                code: 'serpin',
                name: 'Search Pins',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.search'),
                shortcut: 'CTR+ALT+P',
            ),
        ]);
    }

    /**
     * {inheritdoc}
     */
    public function actions(): ?Collection
    {
        return collect([
            new ModuleActionItem(
                owner: 'dashboard.pins',
                name: 'list',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.index')
            ),
            new ModuleActionItem(
                owner: 'dashboard.pins',
                name: 'show',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.show')
            ),
            new ModuleActionItem(
                owner: 'dashboard.pins',
                name: 'save',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.store')
            ),
            new ModuleActionItem(
                owner: 'dashboard.pins',
                name: 'update',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.update')
            ),
            new ModuleActionItem(
                owner: 'dashboard.pins',
                name: 'delete',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.destroy')
            ),
            new ModuleActionItem(
                owner: 'dashboard.search',
                name: 'list',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.index')
            ),
            new ModuleActionItem(
                owner: 'dashboard.search',
                name: 'show',
                requiredAccess: ModuleAccessLevel::READ,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.show')
            ),
            new ModuleActionItem(
                owner: 'dashboard.search',
                name: 'save',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.store')
            ),
            new ModuleActionItem(
                owner: 'dashboard.search',
                name: 'update',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.update')
            ),
            new ModuleActionItem(
                owner: 'dashboard.search',
                name: 'delete',
                requiredAccess: ModuleAccessLevel::WRITE,
                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.destroy')
            ),
        ]);
    }
}
