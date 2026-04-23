<?php

declare(strict_types=1);

namespace Modules\Dashboard\Manifest;

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

final readonly class DashboardManifestProvider implements ManifestProvider
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

    /**
     * @return Collection<int, FeatureNode>
     */
    public function features(): Collection
    {
        return collect([
            new FeatureNode(
                id: 'dashboard.home',
                title: 'Dashboard',
                requiredAccess: ModuleAccessLevel::READ,
                menuItem: new MenuItem(
                    name: 'Dashboard',
                    webPath: '/dashboard',
                    icon: 'home',
                    show: true,
                ),
                nodes: collect([
                    new MenuItem(
                        name: 'Pins',
                        webPath: '/dashboard/pins',
                        icon: 'pin',
                        show: true,
                        actions: collect([
                            new FeatureAction(
                                name: 'list',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.index')
                            ),
                            new FeatureAction(
                                name: 'show',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.show')
                            ),
                            new FeatureAction(
                                name: 'save',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.store')
                            ),
                            new FeatureAction(
                                name: 'update',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.update')
                            ),
                            new FeatureAction(
                                name: 'delete',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.pins.destroy')
                            ),
                        ]),
                    ),
                    new MenuItem(
                        name: 'Search Providers',
                        webPath: '/dashboard/search/providers',
                        icon: 'search',
                        show: true,
                        actions: collect([
                            new FeatureAction(
                                name: 'list',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.index')
                            ),
                            new FeatureAction(
                                name: 'show',
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.show')
                            ),
                            new FeatureAction(
                                name: 'save',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.store')
                            ),
                            new FeatureAction(
                                name: 'update',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.update')
                            ),
                            new FeatureAction(
                                name: 'delete',
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoint: new EndpointBinding(routeName: 'v1.dashboard.search.providers.destroy')
                            ),
                        ]),
                    ),
                ]),
            ),
        ]);
    }
}
