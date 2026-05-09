<?php

declare(strict_types=1);

use App\Repository\Dashboard\Dtos\PageActionItem;
use App\Repository\Dashboard\Dtos\Pins\PinItem;
use App\Repository\Dashboard\Dtos\Pins\SectionItem;
use App\Repository\Dashboard\Dtos\SearchProviders\SearchProviderItem;
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
        ->toContain('Search the web..')
        ->toContain('x-data="webSearch(')
        ->toContain('class="card border border-base-300 bg-base-100 shadow-lg"')
        ->toContain('class="input input-bordered flex h-12 flex-1 items-center gap-3"')
        ->toContain('class="btn btn-primary h-8 md:h-12 px-5"');
});

test('homepage scales search and bookmark cards on large screens', function () {
    $this->withoutVite();

    $html = (string) $this->view('dashboard.homepage.show', [
        'bookmarks' => homepageSections(),
        'modules' => homepageModules(),
        'pageActions' => homepagePageActions(),
        'searchEngines' => homepageSearchEngines(),
    ]);

    expect($html)
        ->toContain('mx-auto max-w-4xl pt-3 sm:pt-8 lg:max-w-[52.08rem]')
        ->toContain('card w-full border border-base-300 bg-base-100 no-underline shadow-sm transition-transform duration-150 hover:-translate-y-1 hover:shadow-lg lg:w-[95%] lg:justify-self-center')
        ->toContain('card-body flex-row items-center gap-3 p-4 lg:gap-[0.7125rem] lg:p-[0.95rem]')
        ->toContain('lg:h-[2.375rem] lg:w-[2.375rem] lg:text-[0.95rem]')
        ->toContain('text-sm font-medium text-base-content lg:text-[0.83125rem]');
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
        ->toContain('href="/dashboard/pins"')
        ->toContain('x-data="pageActionsMenu()"')
        ->toContain('x-on:page-actions:toggle="toggleFromShortcut()"')
        ->toContain('x-on:keydown.down="handleArrowKey($event, 1)"')
        ->toContain('x-on:keydown.up="handleArrowKey($event, -1)"')
        ->toContain('x-on:keydown.enter="handleEnterKey($event)"')
        ->toContain('data-page-actions-root')
        ->toContain('data-page-action-item="enabled"')
        ->toContain('tooltip tooltip-right md:tooltip-left relative inline-flex')
        ->toContain('menu menu-sm w-full gap-1');
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
                    active: true,
                    order: 1,
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
