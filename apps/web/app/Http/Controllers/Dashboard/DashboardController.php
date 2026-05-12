<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\PageBaseController;
use App\Repository\Core\Dtos\SearchHistory\SearchTermActionItem;
use App\Repository\Dashboard\Actions\Pins\LoadUserPinsAction;
use App\Repository\Dashboard\Actions\SearchProviders\LoadUserSearchProvidersAction;
use App\Repository\Dashboard\Dtos\PageActionItem;
use App\Repository\Manifest\Enums\ManifestModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

final class DashboardController extends PageBaseController
{
    /**
     * @throws Throwable
     */
    public function show(
        LoadUserPinsAction $pinsAction,
        LoadUserSearchProvidersAction $providersAction,
    ): View {
        $userId = (int) Auth::id();

        $bookmarks = $pinsAction->handle($userId);
        $searchEngines = $providersAction->handle($userId);
        $modules = $this->getNavigation($userId);

        return view(
            'dashboard.homepage.show',
            [
                'bookmarks' => $bookmarks,
                'searchEngines' => $searchEngines,
                'modules' => $modules,
                'pageActions' => $this->getPageActions(),
                'searchTermActions' => $this->getSearchTermActions(),
            ],
        );
    }

    /**
     * @return Collection<int, PageActionItem>
     */
    protected function getPageActions(): Collection
    {
        return collect([
            new PageActionItem(
                label: 'Pins',
                route: 'dashboard.pins.index',
                icon: '𖤘',
            ),
            new PageActionItem(
                label: 'Search Engines',
                route: 'dashboard.search-providers.index',
                icon: '⌕',
            ),
        ]);
    }

    /**
     * @return Collection<int, SearchTermActionItem>
     */
    private function getSearchTermActions(): Collection
    {
        return collect([
            new SearchTermActionItem(
                name: 'list',
                module: ManifestModule::DASHBOARD->value,
                type: 'external_search',
                route: 'search.terms.index',
            ),
            new SearchTermActionItem(
                name: 'store',
                module: ManifestModule::DASHBOARD->value,
                type: 'external_search',
                route: 'search.terms.store',
            ),
        ]);
    }
}
