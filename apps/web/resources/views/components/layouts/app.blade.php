@props([
    'moduleName' => 'Dashboard',
    'modules'
])

@php
    $user = auth()->user();
    $userName = $user?->name ?? 'User';
    $userEmail = $user?->email ?? '';
    $nameParts = preg_split('/\s+/', trim($userName)) ?: [];
    $firstInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nameParts[0] ?? 'U', 0, 1));
    $lastInitial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nameParts[count($nameParts) > 1 ? count($nameParts) - 1 : 0] ?? '', 0, 1));
    $userInitials = $firstInitial.$lastInitial;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }} — {{ ucwords($moduleName) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    x-data="layoutShell()"
    x-on:keydown.escape.window="closeOpenMenus()"
    x-on:keydown.window="toggleSidebarFromShortcut($event); toggleCommand($event)"
    class="min-h-screen bg-(--lh-bg) text-(--lh-text)"
>

    {{-- Sidebar overlay --}}
    <div
        x-cloak
        x-show="isSidebarOpen"
        x-on:click="closeSidebar()"
        class="fixed inset-0 z-90 bg-(--lh-overlay-bg) backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    {{-- Sidebar --}}
    <nav
        x-cloak
        x-bind:class="{
            '-translate-x-full': ! isSidebarOpen,
            'shadow-(--lh-shadow-lg)': isSidebarOpen,
        }"
        class="fixed top-0 bottom-0 left-0 z-100 flex w-65 flex-col border-r border-(--lh-border) bg-(--lh-sidebar-bg) transition-transform duration-250 ease-in-out"
        aria-label="Main navigation"
    >
        <div class="flex items-center justify-between px-4 pt-4 pb-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <x-logo :size="24" />
                <span class="font-display text-[15px] font-bold text-(--lh-text)">LifeHub</span>
            </a>
            <button
                x-on:click="closeSidebar()"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1 text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-text)"
                aria-label="Close sidebar"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="4" y1="4" x2="14" y2="14"/><line x1="14" y1="4" x2="4" y2="14"/>
                </svg>
            </button>
        </div>
        <div class="min-h-0 flex-1 overflow-y-auto px-2 pb-5">
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
                        <div class="mb-px" x-data="navigationGroup({{ Js::from($isExpanded) }})">
                            <div
                                @class([
                                    'flex w-full items-center rounded-lg transition-colors duration-150',
                                    'bg-(--lh-accent-light) font-semibold text-(color:--lh-accent-text)' => $isActive,
                                    'font-normal text-(color:--lh-text-sec) hover:bg-(--lh-hover)' => ! $isActive,
                                ])
                            >
                                <a
                                    href="{{ resolve_route($navigation->web_path) }}"
                                    class="flex min-w-0 flex-1 items-center gap-2.5 rounded-l-lg px-3 py-2.25 text-sm no-underline"
                                >
                                    <span class="w-5.5 text-center text-xl lg:text-2xl opacity-80">
                                        {{ config('lifehub.icons')[$navigation->icon] }}
                                    </span>
                                    <span class="truncate">{{ $navigation->name }}</span>
                                </a>
                                <button
                                    type="button"
                                    class="flex cursor-pointer rounded-r-lg border-none bg-transparent px-3 py-2.25 text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-text)"
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
                                class="mt-1 space-y-px"
                            >
                                @foreach($children as $child)
                                    <a
                                        href="{{ resolve_route($child->web_path) }}"
                                        class="flex w-full items-center gap-2 rounded-lg py-1.75 pr-3 pl-10 text-[13px] text-(--lh-text-sec) no-underline transition-colors duration-150 hover:bg-(--lh-hover) hover:text-(--lh-text)"
                                    >
                                        <span class="w-4 text-center text-xl lg:text-2xl opacity-70">
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
                                'mb-px flex w-full items-center gap-2.5 rounded-lg px-3 py-2.25 text-sm no-underline transition-colors duration-150',
                                'bg-(--lh-accent-light) font-semibold text-(color:--lh-accent-text)' => $isActive,
                                'font-normal text-(color:--lh-text-sec) hover:bg-(--lh-hover)' => ! $isActive,
                            ])
                        >
                            <span class="w-5.5 text-center text-[15px] opacity-80">
                                {{ config('lifehub.icons')[$navigation->icon] }}
                            </span>
                            {{ $navigation->name }}
                        </a>
                    @endif
                @endforeach
            @endforeach
        </div>
        <div class="border-t border-(--lh-border) px-3 py-3">
            <div class="relative" x-on:click.outside="isSidebarProfileMenuOpen = false">
                <button
                    type="button"
                    x-on:click="toggleSidebarProfileMenu()"
                    class="flex w-full cursor-pointer items-center gap-3 rounded-xl border border-(--lh-border) bg-(--lh-card) px-3 py-2.5 text-left transition-colors duration-150 hover:bg-(--lh-hover)"
                    aria-label="Open profile menu"
                    x-bind:aria-expanded="isSidebarProfileMenuOpen.toString()"
                >
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border-2 border-(--lh-border) bg-(--lh-accent-light) font-display text-[13px] font-semibold text-(--lh-accent-text)">{{ $userInitials }}</span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate text-[13px] font-semibold text-(--lh-text)">{{ $userName }}</span>
                        <span class="block truncate text-[12px] text-(--lh-text-muted)">{{ $userEmail }}</span>
                    </span>
                    <svg
                        x-bind:class="{ 'rotate-180': isSidebarProfileMenuOpen }"
                        class="size-4 shrink-0 text-(--lh-text-muted) transition-transform duration-150"
                        viewBox="0 0 16 16"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M4 6l4 4 4-4" />
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="isSidebarProfileMenuOpen"
                    class="absolute right-0 bottom-full left-0 z-60 mb-2 max-h-[calc(100vh-7rem)] overflow-y-auto rounded-[10px] border border-(--lh-border) bg-(--lh-card) p-1 shadow-(--lh-shadow-lg)"
                >
                    <x-layouts.profile-menu-panel :user-email="$userEmail" :user-name="$userName" />
                </div>
            </div>
        </div>
    </nav>

    {{-- Header --}}
    <header
        class="relative sticky top-0 z-50 flex h-14 items-center gap-2 border-b border-(--lh-border) bg-(--lh-header-bg) px-3 backdrop-blur-md sm:gap-4 sm:px-5"
    >
        {{-- Left: hamburger + logo --}}
        <div class="flex min-w-0 flex-1 items-center gap-2 sm:gap-3">
            <button
                x-on:click="isSidebarOpen = true"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1.5 text-(--lh-text-sec) transition-colors duration-150 hover:bg-(--lh-hover)"
                aria-label="Open navigation"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="3" y1="5" x2="17" y2="5"/><line x1="3" y1="10" x2="17" y2="10"/><line x1="3" y1="15" x2="17" y2="15"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2 no-underline">
                <x-logo :size="28" />
                <span class="hidden font-display text-[17px] font-bold tracking-[-0.3px] text-(--lh-text) sm:inline">LifeHub</span>
            </a>
        </div>

        {{-- Center: module name --}}
        <div class="pointer-events-none absolute inset-x-0 flex justify-center px-16 sm:px-24">
            <span class="block truncate text-center text-[13px] font-semibold tracking-[0.5px] text-(--lh-text-sec) uppercase">
                {{ $module->name }}
            </span>
        </div>

        {{-- Right: theme toggle + profile --}}
        <div class="flex shrink-0 items-center justify-end gap-2">
            <button
                type="button"
                x-on:click="openCommand()"
                class="flex cursor-pointer items-center justify-center rounded-md border-none bg-transparent p-0.5 md:p-1.5 text-2xl md:text-3xl leading-none text-(--lh-text-sec) transition-colors duration-150 hover:bg-(--lh-hover)"
                aria-label="Open command window"
            >𖦏</button>

            <button
                x-on:click="toggleTheme()"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1.5 text-(--lh-text-sec) transition-colors duration-150 hover:bg-(--lh-hover)"
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
                    class="h-8.5 w-8.5 cursor-pointer rounded-full border-2 border-(--lh-border) bg-(--lh-accent-light) font-display text-[13px] font-semibold text-(--lh-accent-text) transition-colors duration-150 hover:border-(--lh-accent)"
                >{{ $userInitials }}</button>

                <div
                    x-cloak
                    x-show="isProfileMenuOpen"
                    class="absolute top-full right-0 z-60 mt-2 min-w-45 rounded-[10px] border border-(--lh-border) bg-(--lh-card) p-1 shadow-(--lh-shadow-lg)"
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
