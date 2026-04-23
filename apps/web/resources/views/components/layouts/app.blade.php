@props([
    'module' => 'Dashboard',
    'manifest' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }} — {{ $module }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-(--lh-bg) text-(--lh-text)">

    {{-- Sidebar overlay --}}
    <div
        id="sidebar-overlay"
        onclick="LifeHub.closeSidebar()"
        class="fixed inset-0 z-90 hidden bg-(--lh-overlay-bg) backdrop-blur-xs"
        aria-hidden="true"
    ></div>

    {{-- Sidebar --}}
    <nav
        id="sidebar"
        class="fixed top-0 bottom-0 left-0 z-100 flex w-65 -translate-x-full flex-col border-r border-(--lh-border) bg-(--lh-sidebar-bg) transition-transform duration-250 ease-in-out"
        aria-label="Main navigation"
    >
        <div class="flex items-center justify-between px-4 pt-4 pb-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <x-logo :size="24" />
                <span class="font-display text-[15px] font-bold text-(--lh-text)">LifeHub</span>
            </a>
            <button
                onclick="LifeHub.closeSidebar()"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1 text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-text)"
                aria-label="Close sidebar"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="4" y1="4" x2="14" y2="14"/><line x1="14" y1="4" x2="4" y2="14"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-2 pb-5">
            @foreach($manifest as $mod)
                @foreach($mod['features'] as $feature)
                    @php $isActive = ($module === $mod['name']); @endphp

                    @if($feature['menu_item']['show'] === false)
                        @continue;
                    @endif

                    <a
                        href="{{ $feature['menu_item']['web_path'] }}"
                        @class([
                            'mb-px flex w-full items-center gap-2.5 rounded-lg px-3 py-2.25 text-sm no-underline transition-colors duration-150',
                            'bg-(--lh-accent-light) font-semibold text-(color:--lh-accent-text)' => $isActive,
                            'font-normal text-(color:--lh-text-sec) hover:bg-(--lh-hover)' => ! $isActive,
                        ])
                    >
                        <span class="w-5.5 text-center text-[15px] opacity-80">
                            {{ config('lifehub.icons')[$feature['menu_item']['icon']] }}
                        </span>
                        {{ $feature['title'] }}
                    </a>
                @endforeach
            @endforeach
        </div>
    </nav>

    {{-- Header --}}
    <header
        class="sticky top-0 z-50 flex h-14 items-center gap-4 border-b border-(--lh-border) bg-(--lh-header-bg) px-5 backdrop-blur-md"
    >
        {{-- Left: hamburger + logo --}}
        <div class="flex items-center gap-3 min-w-50">
            <button
                onclick="LifeHub.openSidebar()"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1.5 text-(--lh-text-sec) transition-colors duration-150 hover:bg-(--lh-hover)"
                aria-label="Open navigation"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="3" y1="5" x2="17" y2="5"/><line x1="3" y1="10" x2="17" y2="10"/><line x1="3" y1="15" x2="17" y2="15"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <x-logo :size="28" />
                <span class="font-display text-[17px] font-bold tracking-[-0.3px] text-(--lh-text)">LifeHub</span>
            </a>
        </div>

        {{-- Center: module name --}}
        <div class="flex-1 text-center">
            <span class="text-[13px] font-semibold tracking-[0.5px] text-(--lh-text-sec) uppercase">
                {{ $module }}
            </span>
        </div>

        {{-- Right: theme toggle + profile --}}
        <div class="min-w-50 flex items-center justify-end gap-2">
            <button
                id="theme-toggle"
                onclick="LifeHub.toggleTheme()"
                class="flex cursor-pointer rounded-md border-none bg-transparent p-1.5 text-(--lh-text-sec) transition-colors duration-150 hover:bg-(--lh-hover)"
                aria-label="Toggle theme"
            >
                <svg id="icon-sun" class="hidden" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                    <circle cx="9" cy="9" r="4"/>
                    <path d="M9 1v2M9 15v2M1 9h2M15 9h2M3.2 3.2l1.4 1.4M13.4 13.4l1.4 1.4M13.4 3.2l1.4 1.4M3.2 13.4l1.4 1.4"/>
                </svg>
                <svg id="icon-moon" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15.1 10.4A7 7 0 017.6 2.9a7 7 0 107.5 7.5z"/>
                </svg>
            </button>

            <div class="relative" id="profile-menu-wrap">
                <button
                    id="profile-btn"
                    onclick="LifeHub.toggleProfileMenu()"
                    class="h-8.5 w-8.5 cursor-pointer rounded-full border-2 border-(--lh-border) bg-(--lh-accent-light) font-display text-[13px] font-semibold text-(--lh-accent-text) transition-colors duration-150 hover:border-(--lh-accent)"
                >{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()?->name ?? ' D', ' '), 1, 1)) }}</button>

                <div
                    id="profile-dropdown"
                    class="absolute top-full right-0 z-60 mt-2 hidden min-w-45 rounded-[10px] border border-(--lh-border) bg-(--lh-card) p-1 shadow-(--lh-shadow-lg)"
                >
                    <div class="border-b border-(--lh-border) px-3.5 py-2.5">
                        <div class="text-[14px] font-semibold text-(--lh-text)">{{ auth()->user()?->name ?? 'User' }}</div>
                        <div class="mt-0.5 text-[12px] text-(--lh-text-muted)">{{ auth()->user()?->email ?? '' }}</div>
                    </div>
                    <a href="#" class="block w-full rounded-md px-3.5 py-2 text-[13px] text-(--lh-text) no-underline hover:bg-(--lh-hover)"
                    >Profile</a>
                    <a href="#" class="block w-full rounded-md px-3.5 py-2 text-[13px] text-(--lh-text) no-underline hover:bg-(--lh-hover)"
                    >Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="block w-full cursor-pointer rounded-md border-none bg-transparent px-3.5 py-2 text-left font-[inherit] text-[13px] text-[#e54] transition-colors duration-150 hover:bg-(--lh-hover)"
                        >Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

</body>
</html>
