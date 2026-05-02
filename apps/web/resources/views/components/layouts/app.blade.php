@props([
    'moduleName' => 'Dashboard',
    'modules'
])

@php
    $user = auth()->user();
    $userName = $user?->name ?? 'User';
    $userEmail = $user?->email ?? '';
    $activeModule = collect($modules)->firstWhere('key', $moduleName);
    $nameParts = preg_split('/\s+/', trim($userName)) ?: [];
    $firstInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nameParts[0] ?? 'U', 0, 1));
    $lastInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nameParts[count($nameParts) > 1 ? count($nameParts) - 1 : 0] ?? '', 0, 1));
    $userInitials = $firstInitial.$lastInitial;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-base-200">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }} — {{ ucwords($moduleName) }}</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('lh_theme') === 'dark' ? 'forest' : 'emerald';
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="layoutShell()"
    x-on:keydown.escape.window="closeOpenMenus()"
    x-on:keydown.window="toggleSidebarFromShortcut($event); toggleCommand($event)"
    class="min-h-screen bg-base-200 text-base-content"
>

    {{-- Sidebar overlay --}}
    <div
        x-cloak
        x-show="isSidebarOpen"
        x-on:click="closeSidebar()"
        class="fixed inset-0 z-90 bg-base-content/35 backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    {{-- Sidebar --}}
    <nav
        x-cloak
        x-bind:class="{
            '-translate-x-full': ! isSidebarOpen,
            'shadow-2xl': isSidebarOpen,
        }"
        class="fixed top-0 bottom-0 left-0 z-100 flex w-72 flex-col border-r border-base-300 bg-base-100 transition-transform duration-250 ease-in-out"
        aria-label="Main navigation"
    >
        <div class="flex items-center justify-between border-b border-base-300 px-4 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 no-underline">
                <x-logo :size="24" />
                <span class="font-display text-sm font-bold text-base-content">LifeHub</span>
            </a>
            <button
                type="button"
                x-on:click="closeSidebar()"
                class="btn btn-ghost btn-sm btn-square"
                aria-label="Close sidebar"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="4" y1="4" x2="14" y2="14"/><line x1="14" y1="4" x2="4" y2="14"/>
                </svg>
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto px-3 py-4">
            @foreach($modules as $module)
                @php $isActive = ($moduleName === $module->key); @endphp

                @foreach($module->navigation ?? collect() as $navigation)
                    @if($navigation->show === false)
                        @continue;
                    @endif

                    @php
                        $children = $navigation->children?->filter(fn ($child) => $child->show !== false) ?? collect();
                        $hasChildren = $children->isNotEmpty();
                        $childrenId = \Illuminate\Support\Str::slug("navigation-{$module->key}-{$navigation->key}");
                        $isExpanded = $children->contains(fn ($child) => request()->is(ltrim($child->web_path, '/')));
                    @endphp

                    @if($hasChildren)
                        <div class="mb-1" x-data="navigationGroup({{ Js::from($isExpanded) }})">
                            <div
                                @class([
                                    'flex w-full items-center rounded-box border transition-colors duration-150',
                                    'border-primary/15 bg-primary/10 text-success' => $isActive,
                                    'border-transparent text-base-content/75 hover:border-base-300 hover:bg-base-200' => ! $isActive,
                                ])
                            >
                                <a
                                    href="{{ resolve_route($navigation->web_path) }}"
                                    class="flex min-w-0 flex-1 items-center gap-3 rounded-l-box px-4 py-3 text-sm no-underline"
                                >
                                    <span class="w-6 text-center text-lg opacity-80">
                                        {{ config('lifehub.icons')[$navigation->icon] }}
                                    </span>
                                    <span class="truncate">{{ $navigation->name }}</span>
                                </a>
                                <button
                                    type="button"
                                    class="flex cursor-pointer rounded-r-box border-none bg-transparent px-4 py-3 text-base-content/55 transition-colors duration-150 hover:text-base-content"
                                    aria-label="Toggle {{ $navigation->name }} submenu"
                                    x-bind:aria-expanded="isExpanded.toString()"
                                    aria-controls="{{ $childrenId }}"
                                    x-on:click="toggle()"
                                >
                                    <svg
                                        x-bind:class="{ 'rotate-90': isExpanded }"
                                        class="size-3.5 transition-transform duration-150"
                                        viewBox="0 0 16 16"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        aria-hidden="true"
                                    >
                                        <path d="M6 4l4 4-4 4" />
                                    </svg>
                                </button>
                            </div>
                            <div
                                id="{{ $childrenId }}"
                                @if(! $isExpanded) x-cloak @endif
                                x-show="isExpanded"
                                class="mt-2 space-y-1"
                            >
                                @foreach($children as $child)
                                    <a
                                        href="{{ resolve_route($child->web_path) }}"
                                        class="flex w-full items-center gap-3 rounded-box py-2 pr-3 pl-10 text-sm text-base-content/70 no-underline transition-colors duration-150 hover:bg-base-200 hover:text-base-content"
                                    >
                                        <span class="w-4 text-center text-base opacity-70">
                                            {{ config('lifehub.icons')[$child->icon] }}
                                        </span>
                                        <span class="truncate">{{ $child->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ resolve_route($navigation->web_path) }}"
                            @class([
                                'mb-1 flex w-full items-center gap-3 rounded-box border px-4 py-3 text-sm no-underline transition-colors duration-150',
                                'border-primary/15 bg-primary/10 font-semibold text-primary' => $isActive,
                                'border-transparent font-normal text-base-content/75 hover:border-base-300 hover:bg-base-200' => ! $isActive,
                            ])
                        >
                            <span class="w-6 text-center text-base opacity-80">
                                {{ config('lifehub.icons')[$navigation->icon] }}
                            </span>
                            {{ $navigation->name }}
                        </a>
                    @endif
                @endforeach
            @endforeach
        </div>
    </nav>

    {{-- Header --}}
    <header
        class="navbar sticky top-0 z-50 min-h-16 border-b border-base-300 bg-base-100/85 px-3 shadow-sm backdrop-blur-md sm:px-5"
    >
        {{-- Left: hamburger + logo --}}
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
            <button
                type="button"
                x-on:click="isSidebarOpen = true"
                class="btn btn-ghost btn-sm btn-square"
                aria-label="Open navigation"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="3" y1="5" x2="17" y2="5"/><line x1="3" y1="10" x2="17" y2="10"/><line x1="3" y1="15" x2="17" y2="15"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 no-underline">
                <x-logo :size="28" />
                <span class="hidden font-display text-[17px] font-bold tracking-[-0.3px] text-base-content sm:inline">LifeHub</span>
            </a>
        </div>

        {{-- Center: module name --}}
        <div class="pointer-events-none absolute inset-x-0 flex justify-center px-16 sm:px-24">
            <span class="block truncate text-center text-xs font-semibold tracking-[0.5px] text-base-content/65 uppercase">
                {{ $activeModule?->name ?? ucfirst($moduleName) }}
            </span>
        </div>

        {{-- Right: theme toggle + profile --}}
        <div class="flex shrink-0 items-center justify-end gap-2">
            <button
                type="button"
                x-on:click="openCommand()"
                class="btn btn-ghost btn-sm rounded-full px-3 text-base"
                aria-label="Open command window"
            >⌘/</button>

            <button
                type="button"
                x-on:click="toggleTheme()"
                class="btn btn-ghost btn-sm btn-square"
                aria-label="Toggle theme"
            >
                <svg x-cloak x-show="isDark" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                    <circle cx="9" cy="9" r="4"/>
                    <path d="M9 1v2M9 15v2M1 9h2M15 9h2M3.2 3.2l1.4 1.4M13.4 13.4l1.4 1.4M13.4 3.2l1.4 1.4M3.2 13.4l1.4 1.4"/>
                </svg>
                <svg x-show="! isDark" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.1 10.4A7 7 0 017.6 2.9a7 7 0 107.5 7.5z"/>
                </svg>
            </button>

            <div class="relative" x-on:click.outside="isProfileMenuOpen = false">
                <button
                    type="button"
                    x-on:click="toggleHeaderProfileMenu()"
                    class="cursor-pointer"
                >
                    <span class="avatar placeholder">
                        <span class="bg-primary text-primary-content w-9 rounded font-display text-sm font-semibold">{{ $userInitials }}</span>
                    </span>
                </button>

                <div
                    x-cloak
                    x-show="isProfileMenuOpen"
                    class="absolute top-full right-0 z-60 mt-2 min-w-48 rounded-box border border-base-300 bg-base-100 shadow-lg"
                >
                    <x-layouts.profile-menu-panel :user-email="$userEmail" :user-name="$userName" />
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <x-command-modal />

</body>
</html>
