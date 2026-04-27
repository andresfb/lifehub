<?php

declare(strict_types=1);

use App\Repository\Dashboard\Dtos\PinItem;
use App\Repository\Dashboard\Dtos\SectionItem;
use App\Repository\Manifest\Dtos\ModuleItem;
use App\Repository\Manifest\Dtos\NavigationItem;
use Illuminate\Support\Collection;

test('homepage search button keeps its icon centered', function () {
    $this->withoutVite();

    $html = (string) $this->view('dashboard.homepage.show', [
        'bookmarks' => homepageSections(),
        'modules' => homepageModules(),
        'searchEngines' => homepageSearchEngines(),
    ]);

    expect($html)
        ->toContain('Search the web...')
        ->toContain('min-w-0')
        ->toContain('flex h-12 w-12 shrink-0 cursor-pointer items-center justify-center self-stretch border-none bg-transparent text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-accent)');
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
 * @return array<int, array{name: string, url: string, icon: string, colorClass: string}>
 */
function homepageSearchEngines(): array
{
    return [
        [
            'name' => 'DuckDuckGo',
            'url' => 'https://noai.duckduckgo.com/?ia=web&origin=lifehub&q=',
            'icon' => 'D',
            'colorClass' => 'bg-[#DE5833]',
        ],
    ];
}
