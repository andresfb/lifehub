@props(['module' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }} — {{ $module }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen" style="background:var(--lh-bg);color:var(--lh-text)">

    {{-- Sidebar overlay --}}
    <div
        id="sidebar-overlay"
        onclick="LifeHub.closeSidebar()"
        class="fixed inset-0 z-90 hidden"
        style="background:var(--lh-overlay-bg);backdrop-filter:blur(4px)"
        aria-hidden="true"
    ></div>

    {{-- Sidebar --}}
    <nav
        id="sidebar"
        class="fixed top-0 left-0 bottom-0 z-100 w-65 flex flex-col"
        style="background:var(--lh-sidebar-bg);border-right:1px solid var(--lh-border);translate:-100% 0;transition:translate 0.25s cubic-bezier(0.4,0,0.2,1);box-shadow:none"
        aria-label="Main navigation"
    >
        <div class="flex items-center justify-between px-4 pt-4 pb-3">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <x-logo :size="24" />
                <span class="font-display font-bold text-[15px]" style="color:var(--lh-text)">LifeHub</span>
            </a>
            <button
                onclick="LifeHub.closeSidebar()"
                class="p-1 rounded-md border-none bg-transparent cursor-pointer flex"
                style="color:var(--lh-text-muted);transition:color 0.12s"
                onmouseenter="this.style.color='var(--lh-text)'"
                onmouseleave="this.style.color='var(--lh-text-muted)'"
                aria-label="Close sidebar"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="4" y1="4" x2="14" y2="14"/><line x1="14" y1="4" x2="4" y2="14"/>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-2 pb-5">
            @foreach(config('lifehub.modules', []) as $mod)
                @php $isActive = ($module === $mod['name']); @endphp
                <a
                    href="{{ route($mod['route'] ?? 'dashboard') }}"
                    class="flex items-center gap-2.5 w-full px-3 py-2.25 rounded-lg no-underline mb-px text-sm"
                    style="background:{{ $isActive ? 'var(--lh-accent-light)' : 'transparent' }};color:{{ $isActive ? 'var(--lh-accent-text)' : 'var(--lh-text-sec)' }};font-weight:{{ $isActive ? '600' : '400' }};transition:all 0.12s"
                    @unless($isActive)
                        onmouseenter="this.style.background='var(--lh-hover)'"
                        onmouseleave="this.style.background='transparent'"
                    @endunless
                >
                    <span class="w-5.5 text-center text-[15px] opacity-80">{{ $mod['icon'] }}</span>
                    {{ $mod['name'] }}
                </a>
            @endforeach
        </div>
    </nav>

    {{-- Header --}}
    <header
        class="h-14 flex items-center gap-4 px-5 sticky top-0 z-50"
        style="border-bottom:1px solid var(--lh-border);background:var(--lh-header-bg);backdrop-filter:blur(12px)"
    >
        {{-- Left: hamburger + logo --}}
        <div class="flex items-center gap-3 min-w-50">
            <button
                onclick="LifeHub.openSidebar()"
                class="p-1.5 rounded-md border-none bg-transparent cursor-pointer flex"
                style="color:var(--lh-text-sec);transition:background 0.15s"
                onmouseenter="this.style.background='var(--lh-hover)'"
                onmouseleave="this.style.background='transparent'"
                aria-label="Open navigation"
            >
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <line x1="3" y1="5" x2="17" y2="5"/><line x1="3" y1="10" x2="17" y2="10"/><line x1="3" y1="15" x2="17" y2="15"/>
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 no-underline">
                <x-logo :size="28" />
                <span class="font-display font-bold text-[17px] tracking-[-0.3px]" style="color:var(--lh-text)">LifeHub</span>
            </a>
        </div>

        {{-- Center: module name --}}
        <div class="flex-1 text-center">
            <span class="text-[13px] font-semibold tracking-[0.5px] uppercase" style="color:var(--lh-text-sec)">
                {{ $module }}
            </span>
        </div>

        {{-- Right: theme toggle + profile --}}
        <div class="min-w-50 flex items-center justify-end gap-2">
            <button
                id="theme-toggle"
                onclick="LifeHub.toggleTheme()"
                class="p-1.5 rounded-md border-none bg-transparent cursor-pointer flex"
                style="color:var(--lh-text-sec);transition:background 0.15s"
                onmouseenter="this.style.background='var(--lh-hover)'"
                onmouseleave="this.style.background='transparent'"
                aria-label="Toggle theme"
            >
                <svg id="icon-sun" width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" style="display:none">
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
                    class="w-8.5 h-8.5 rounded-full cursor-pointer font-display font-semibold text-[13px] border-2"
                    style="background:var(--lh-accent-light);color:var(--lh-accent-text);border-color:var(--lh-border);transition:border-color 0.15s"
                    onmouseenter="this.style.borderColor='var(--lh-accent)'"
                    onmouseleave="this.style.borderColor='var(--lh-border)'"
                >{{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 1)) }}{{ strtoupper(substr(strstr(auth()->user()?->name ?? ' D', ' '), 1, 1)) }}</button>

                <div
                    id="profile-dropdown"
                    class="absolute top-full right-0 mt-2 hidden z-60 rounded-[10px] p-1 min-w-45"
                    style="background:var(--lh-card);border:1px solid var(--lh-border);box-shadow:var(--lh-shadow-lg)"
                >
                    <div class="px-3.5 py-2.5" style="border-bottom:1px solid var(--lh-border)">
                        <div class="font-semibold text-[14px]" style="color:var(--lh-text)">{{ auth()->user()?->name ?? 'User' }}</div>
                        <div class="text-[12px] mt-0.5" style="color:var(--lh-text-muted)">{{ auth()->user()?->email ?? '' }}</div>
                    </div>
                    <a href="#" class="block w-full px-3.5 py-2 rounded-md text-[13px] no-underline"
                        style="color:var(--lh-text)"
                        onmouseenter="this.style.background='var(--lh-hover)'"
                        onmouseleave="this.style.background='transparent'"
                    >Profile</a>
                    <a href="#" class="block w-full px-3.5 py-2 rounded-md text-[13px] no-underline"
                        style="color:var(--lh-text)"
                        onmouseenter="this.style.background='var(--lh-hover)'"
                        onmouseleave="this.style.background='transparent'"
                    >Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="block w-full text-left px-3.5 py-2 rounded-md text-[13px] border-none bg-transparent cursor-pointer font-[inherit]"
                            style="color:#e54;transition:background 0.12s"
                            onmouseenter="this.style.background='var(--lh-hover)'"
                            onmouseleave="this.style.background='transparent'"
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
