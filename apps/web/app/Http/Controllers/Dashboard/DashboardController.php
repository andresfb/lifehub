<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\LoadUserPinsAction;
use App\Actions\LoadUserSearchProvidersAction;
use App\Dtos\PageActionItem;
use App\Http\Controllers\PageBaseController;
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
                route: '', // TODO: add search engines route
                icon: '⌕',
            ),
        ]);
    }
}
