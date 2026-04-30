<?php

declare(strict_types=1);

use App\Dtos\PageActionItem;
use App\Repository\Dashboard\Dtos\PinItem;
use App\Repository\Dashboard\Dtos\SearchProviderItem;
use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Manifest\Dtos\ModuleItem;
use App\Repository\Manifest\Dtos\NavigationItem;
use Illuminate\Support\Collection;

test('homepage search button keeps its icon centered', function () {
    $this->withoutVite();

    $html = (string) $this->view('dashboard.homepage.show', [
        'bookmarks' => homepageSections(),
        'modules' => homepageModules(),
        'pageActions' => homepagePageActions(),
        'searchEngines' => homepageSearchEngines(),
    ]);

    expect($html)
        ->toContain('Search the web...')
        ->toContain('min-w-0')
        ->toContain('x-data="webSearch(')
        ->toContain('flex h-12 w-12 shrink-0 cursor-pointer items-center justify-center self-stretch border-none bg-transparent text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-accent)');
});

test('web search replaces the engine placeholder with the encoded query', function () {
    $script = file_get_contents(resource_path('js/app.js'));

    expect($script)
        ->not->toBeFalse()
        ->toContain("this.selectedEngine.url.replace('%s', encodeURIComponent(query))")
        ->not->toContain('this.selectedEngine.url + encodeURIComponent(query)');
});

test('homepage renders responsive page actions menu', function () {
    $this->withoutVite();

    $html = (string) $this->view('dashboard.homepage.show', [
        'bookmarks' => homepageSections(),
        'modules' => homepageModules(),
        'pageActions' => homepagePageActions(),
        'searchEngines' => homepageSearchEngines(),
    ]);

    expect($html)
        ->toContain('Manage')
        ->toContain('𖦏')
        ->toContain('Pins')
        ->toContain('Search Engines')
        ->toContain('href="dashboard/pins"')
        ->toContain('sm:hidden')
        ->toContain('sm:inline');
});

/**
 * @return Collection<int, SectionItem>
 */
function homepageSections(): Collection
{
    return collect([
        new SectionItem(
            id: 1,
            slug: 'favorites',
            name: 'Favorites',
            order: 1,
            items: collect([
                new PinItem(
                    slug: 'lifehub',
                    title: 'LifeHub',
                    url: 'https://example.com',
                    order: '1',
                ),
            ]),
        ),
    ]);
}

/**
 * @return Collection<int, ModuleItem>
 */
function homepageModules(): Collection
{
    return collect([
        new ModuleItem(
            key: 'dashboard',
            name: 'Dashboard',
            description: 'Homepage',
            isPublic: false,
            sortOrder: 1,
            navigation: collect([
                new NavigationItem(
                    key: 'dashboard',
                    name: 'Dashboard',
                    web_path: '/dashboard',
                    icon: 'home',
                    show: true,
                    sort_order: 1,
                ),
            ]),
        ),
    ]);
}

/**
 * @return Collection<int, PageActionItem>
 */
function homepagePageActions(): Collection
{
    return collect([
        new PageActionItem(
            label: 'Pins',
            route: 'dashboard.pins.index',
            icon: '𖤘',
        ),
        new PageActionItem(
            label: 'Search Engines',
            route: '',
            icon: '⌕',
        ),
    ]);
}

/**
 * @return Collection<int, SearchProviderItem>
 */
function homepageSearchEngines(): Collection
{
    return collect([
        new SearchProviderItem(
            id: 1,
            name: 'DuckDuckGo',
            url: 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=%s',
            default: true,
            order: 1,
            icon: 'D',
            icon_color: 'bg-[#DE5833]',
        ),
    ]);
}
