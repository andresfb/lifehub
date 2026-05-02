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
        ->toMatch('/id="navigation-reports-reports"\s+x-show="isExpanded"\s+class="mt-2 space-y-1"/');
});

test('layout wires the sidebar keyboard shortcut', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('x-data="layoutShell()"')
        ->toContain('x-on:keydown.window="toggleSidebarFromShortcut($event); togglePageActionsFromShortcut($event); toggleCommand($event)"');

    expect(file_get_contents(resource_path('js/app.js')))
        ->toContain("!event.metaKey || event.key !== '2'")
        ->toContain("document.querySelectorAll('[data-page-actions-root]')")
        ->toContain("new CustomEvent('page-actions:toggle')");
});

test('layout renders a header button that opens the command window', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('aria-label="Open command window"')
        ->toContain('x-on:click="openCommand()"')
        ->toContain('>⌘/</button>');

    expect(file_get_contents(resource_path('js/app.js')))
        ->toContain('openCommand()')
        ->toContain('this.closeSidebar()')
        ->toContain('this.closeProfileMenus()');
});

test('layout renders the header profile access menu', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('x-on:click="toggleHeaderProfileMenu()"')
        ->toContain('x-show="isProfileMenuOpen"')
        ->toContain('<li><a href="#">Profile</a></li>')
        ->toContain('<li><a href="#">Settings</a></li>')
        ->toContain('action="'.route('logout').'"');
});

test('layout shell closes both profile menus together', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('x-show="isProfileMenuOpen"')
        ->toContain('x-on:click="toggleHeaderProfileMenu()"');

    expect(file_get_contents(resource_path('js/app.js')))
        ->toContain('isSidebarProfileMenuOpen: false')
        ->toContain('closeProfileMenus()')
        ->toContain('closeSidebar()');
});

test('layout header collapses cleanly on small screens', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('navbar sticky top-0 z-50 min-h-16 border-b border-base-300 bg-base-100/85 px-3 shadow-sm backdrop-blur-md sm:px-5')
        ->toContain('flex min-w-0 flex-1 items-center gap-2 sm:gap-3')
        ->toContain('hidden font-display text-[17px] font-bold tracking-[-0.3px] text-base-content sm:inline')
        ->toContain('pointer-events-none absolute inset-x-0 flex justify-center px-16 sm:px-24')
        ->toContain('flex shrink-0 items-center justify-end gap-2');
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
