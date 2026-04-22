<?php

declare(strict_types=1);

namespace Modules\Dashboard\Manifest;

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
                kind: FeatureKind::Group,
                requiredAccess: ModuleAccessLevel::READ,
                nav: new NavHints(
                    webPath: '/dashboard',
                    tuiCommand: 'dashboard',
                    icon: 'home',
                    showInMenu: true,
                ),
                endpoints: collect([
                    new EndpointBinding(routeName: 'v1.dashboard'),
                ]),
                children: collect([
                    new FeatureNode(
                        id: 'dashboard.pins',
                        title: 'Pins',
                        kind: FeatureKind::Group,
                        requiredAccess: ModuleAccessLevel::READ,
                        nav: new NavHints(webPath: '/dashboard/pins', icon: 'pin'),
                        children: collect([
                            new FeatureNode(
                                id: 'dashboard.pins.list',
                                title: 'View Pins',
                                kind: FeatureKind::Screen,
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoints: collect([
                                    new EndpointBinding(routeName: 'v1.dashboard.pins.index'),
                                    new EndpointBinding(routeName: 'v1.dashboard.pins.show'),
                                ]),
                            ),
                            new FeatureNode(
                                id: 'dashboard.pins.manage',
                                title: 'Manage Pins',
                                kind: FeatureKind::Action,
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoints: collect([
                                    new EndpointBinding(routeName: 'v1.dashboard.pins.store'),
                                    new EndpointBinding(routeName: 'v1.dashboard.pins.update'),
                                    new EndpointBinding(routeName: 'v1.dashboard.pins.destroy'),
                                ]),
                            ),
                        ]),
                    ),
                    new FeatureNode(
                        id: 'dashboard.search',
                        title: 'Search Providers',
                        kind: FeatureKind::Group,
                        requiredAccess: ModuleAccessLevel::READ,
                        nav: new NavHints(webPath: '/dashboard/search', icon: 'search'),
                        children: collect([
                            new FeatureNode(
                                id: 'dashboard.search.list',
                                title: 'View Search Providers',
                                kind: FeatureKind::Screen,
                                requiredAccess: ModuleAccessLevel::READ,
                                endpoints: collect([
                                    new EndpointBinding(routeName: 'v1.dashboard.search.providers.index'),
                                    new EndpointBinding(routeName: 'v1.dashboard.search.providers.show'),
                                ]),
                            ),
                            new FeatureNode(
                                id: 'dashboard.search.manage',
                                title: 'Manage Search Providers',
                                kind: FeatureKind::Action,
                                requiredAccess: ModuleAccessLevel::WRITE,
                                endpoints: collect([
                                    new EndpointBinding(routeName: 'v1.dashboard.search.providers.store'),
                                    new EndpointBinding(routeName: 'v1.dashboard.search.providers.update'),
                                    new EndpointBinding(routeName: 'v1.dashboard.search.providers.destroy'),
                                ]),
                            ),
                        ]),
                    ),
                ]),
            ),
        ]);
    }
}
