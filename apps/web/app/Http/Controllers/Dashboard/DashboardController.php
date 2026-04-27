<?php

declare(strict_types=1);

namespace App\Http\Controllers\Dashboard;

use App\Actions\LoadUserPinsAction;
use App\Dtos\PageActionItem;
use App\Http\Controllers\PageBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Throwable;

final class DashboardController extends PageBaseController
{
    /**
     * @throws Throwable
     */
    public function show(Request $request, LoadUserPinsAction $pinsAction): View
    {
        $user = $request->user();

        $bookmarks = $pinsAction->handle($user);
        $searchEngines = $this->searchEngines();
        $modules = $this->getNavigation($user);

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

    /** @return array<int, array{name: string, url: string, icon: string, colorClass: string}> */
    private function searchEngines(): array
    {
        return [
            ['name' => 'DuckDuckGo', 'url' => 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=', 'icon' => 'D', 'colorClass' => 'bg-[#DE5833]'],
            ['name' => 'Google',     'url' => 'https://www.google.com/search?q=',             'icon' => 'G', 'colorClass' => 'bg-[#4285F4]'],
            ['name' => 'Bing',       'url' => 'https://www.bing.com/search?q=',               'icon' => 'B', 'colorClass' => 'bg-[#008373]'],
            ['name' => 'Brave',      'url' => 'https://search.brave.com/search?q=',           'icon' => '🦁', 'colorClass' => 'bg-[#FB542B]'],
            ['name' => 'YouTube',    'url' => 'https://www.youtube.com/results?search_query=', 'icon' => '▶', 'colorClass' => 'bg-[#FF0000]'],
            ['name' => 'Reddit',     'url' => 'https://www.reddit.com/search/?q=',            'icon' => '◉', 'colorClass' => 'bg-[#FF4500]'],
        ];
    }
}
