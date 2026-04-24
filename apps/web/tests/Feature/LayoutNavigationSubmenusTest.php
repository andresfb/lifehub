<?php

declare(strict_types=1);

use App\Repository\Manifest\Dtos\ModuleItem;
use App\Repository\Manifest\Dtos\NavigationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

test('layout renders visible child navigation under its parent', function () {
    $this->withoutVite();

    $view = $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    $view
        ->assertSee('Reports')
        ->assertSee('Activity');
});

test('layout does not render hidden child navigation', function () {
    $this->withoutVite();

    $view = $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules(includeHiddenChild: true)]
    );

    $view
        ->assertSee('Activity')
        ->assertDontSee('Archived');
});

test('layout opens a parent submenu when a child path is active', function () {
    $this->withoutVite();
    app()->instance('request', Request::create('/reports/activity'));

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('x-data="navigationGroup(true)"')
        ->toContain('x-bind:aria-expanded="isExpanded.toString()"')
        ->toMatch('/id="navigation-reports-reports"\s+x-show="isExpanded"\s+class="mt-1 space-y-px"/');
});

/**
 * @return Collection<int, ModuleItem>
 */
function layoutNavigationSubmenuModules(bool $includeHiddenChild = false): Collection
{
    $children = collect([
        new NavigationItem(
            key: 'activity',
            name: 'Activity',
            web_path: '/reports/activity',
            icon: 'home',
            show: true,
            sort_order: 1,
        ),
    ]);

    if ($includeHiddenChild) {
        $children->push(new NavigationItem(
            key: 'archived',
            name: 'Archived',
            web_path: '/reports/archived',
            icon: 'home',
            show: false,
            sort_order: 2,
        ));
    }

    return collect([
        new ModuleItem(
            key: 'reports',
            name: 'Reports',
            description: 'Reporting',
            isPublic: false,
            sortOrder: 1,
            navigation: collect([
                new NavigationItem(
                    key: 'reports',
                    name: 'Reports',
                    web_path: '/reports',
                    icon: 'home',
                    show: true,
                    sort_order: 1,
                    children: $children,
                ),
            ]),
        ),
    ]);
}
