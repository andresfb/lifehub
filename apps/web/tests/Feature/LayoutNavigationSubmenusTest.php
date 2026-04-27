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

test('layout wires the sidebar keyboard shortcut', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('x-data="layoutShell()"')
        ->toContain('x-on:keydown.window="toggleSidebarFromShortcut($event); toggleCommand($event)"');
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
        ->toContain('>𖦏</button>');

    expect(file_get_contents(resource_path('js/app.js')))
        ->toContain('openCommand()')
        ->toContain('this.closeSidebar()')
        ->toContain('this.closeProfileMenus()');
});

test('layout renders the sidebar profile access menu', function () {
    $this->withoutVite();

    $html = (string) $this->blade(
        '<x-layouts.app module-name="reports" :modules="$modules">Content</x-layouts.app>',
        ['modules' => layoutNavigationSubmenuModules()]
    );

    expect($html)
        ->toContain('Open profile menu')
        ->toContain('x-show="isSidebarProfileMenuOpen"')
        ->toContain('min-h-0 flex-1 overflow-y-auto')
        ->toContain('max-h-[calc(100vh-7rem)] overflow-y-auto')
        ->toContain('>Profile</a>')
        ->toContain('>Settings</a>')
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
        ->toContain('x-show="isSidebarProfileMenuOpen"')
        ->toContain('x-on:click="toggleHeaderProfileMenu()"')
        ->toContain('x-on:click="toggleSidebarProfileMenu()"');

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
        ->toContain('px-3 backdrop-blur-md sm:gap-4 sm:px-5')
        ->toContain('flex min-w-0 flex-1 items-center gap-2 sm:gap-3')
        ->toContain('hidden font-display text-[17px] font-bold tracking-[-0.3px] text-(--lh-text) sm:inline')
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
