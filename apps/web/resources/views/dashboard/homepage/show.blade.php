<x-layouts.app module="Dashboard">

    {{-- Search bar --}}
    <div style="max-width:640px;margin:0 auto;padding:32px 20px 8px">
        <div
            id="search-wrap"
            class="flex items-center h-12 rounded-[14px]"
            style="background:var(--lh-input);border:2px solid var(--lh-border);box-shadow:var(--lh-shadow);transition:border-color 0.2s,box-shadow 0.2s"
        >
            {{-- Engine selector --}}
            <div class="relative shrink-0" id="engine-wrap">
                <button
                    type="button"
                    id="engine-btn"
                    onclick="LifeHub.toggleEngineDropdown()"
                    class="flex items-center gap-1.5 h-12 border-none bg-transparent cursor-pointer text-[13px] font-medium pl-3.5 pr-1"
                    style="color:var(--lh-text-sec);font-family:inherit"
                >
                    <span id="engine-name">{{ $searchEngines[0]['name'] }}</span>
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <path d="M2.5 4L5 6.5 7.5 4"/>
                    </svg>
                </button>

                <div
                    id="engine-dropdown"
                    class="absolute top-full left-2 mt-1 hidden z-20 rounded-[10px] p-1 min-w-42.5"
                    style="background:var(--lh-card);border:1px solid var(--lh-border);box-shadow:var(--lh-shadow-lg)"
                >
                    @foreach($searchEngines as $engine)
                        <button
                            type="button"
                            data-url="{{ $engine['url'] }}"
                            data-name="{{ $engine['name'] }}"
                            onclick="LifeHub.selectEngine(this)"
                            class="flex items-center gap-2 w-full px-2.5 py-2 border-none rounded-md text-[13px] cursor-pointer text-left"
                            style="background:{{ $loop->first ? 'var(--lh-accent-light)' : 'none' }};color:var(--lh-text);font-family:inherit;transition:background 0.1s"
                            onmouseenter="this.style.background='var(--lh-hover)'"
                            onmouseleave="this.style.background='none'"
                        >
                            <span class="w-5 h-5 rounded-[5px] flex items-center justify-center text-[10px] font-bold shrink-0 text-white" style="background:{{ $engine['color'] }}">{{ $engine['icon'] }}</span>
                            {{ $engine['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="w-px h-6 shrink-0" style="background:var(--lh-border)"></div>

            {{-- Search input --}}
            <input
                id="search-input"
                type="text"
                placeholder="Search the web..."
                class="flex-1 h-full border-none bg-transparent px-3.5 text-[15px]"
                style="color:var(--lh-text);font-family:inherit"
                onfocus="document.getElementById('search-wrap').style.borderColor='var(--lh-accent)';document.getElementById('search-wrap').style.boxShadow='0 0 0 3px oklch(0.65 0.15 175 / 0.13)'"
                onblur="document.getElementById('search-wrap').style.borderColor='var(--lh-border)';document.getElementById('search-wrap').style.boxShadow='var(--lh-shadow)'"
                onkeydown="if(event.key==='Enter') LifeHub.doSearch()"
            />

            {{-- Search button --}}
            <button
                type="button"
                onclick="LifeHub.doSearch()"
                class="px-4 h-full border-none bg-transparent cursor-pointer flex items-center"
                style="color:var(--lh-text-muted);transition:color 0.12s"
                onmouseenter="this.style.color='var(--lh-accent)'"
                onmouseleave="this.style.color='var(--lh-text-muted)'"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="7.5" cy="7.5" r="5"/><line x1="11.5" y1="11.5" x2="16" y2="16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Bookmarks grid --}}
    <div style="padding:20px 24px 40px;max-width:1320px;margin:0 auto">
        @foreach($bookmarks as $group)
            <div class="mb-7">
                <h3 class="text-[12px] font-semibold uppercase tracking-[1px] mb-2.5 pl-1" style="color:var(--lh-text-muted)">
                    {{ $group['category'] }}
                </h3>
                <div class="grid gap-2" style="grid-template-columns:repeat(6,1fr)">
                    @foreach($group['items'] as $bookmark)
                        <a
                            href="{{ $bookmark['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="flex items-center gap-2.5 px-3 py-2.5 rounded-[10px] no-underline"
                            style="background:var(--lh-card);border:1px solid var(--lh-border-light);transition:all 0.15s;cursor:pointer"
                            onmouseenter="this.style.background='var(--lh-hover)';this.style.borderColor='var(--lh-border)';this.style.transform='translateY(-1px)'"
                            onmouseleave="this.style.background='var(--lh-card)';this.style.borderColor='var(--lh-border-light)';this.style.transform='none'"
                        >
                            <span
                                class="w-8 h-8 rounded-lg flex items-center justify-center text-[13px] font-bold shrink-0 text-white font-display"
                                style="background:{{ $bookmark['iconBg'] }}"
                            >{{ $bookmark['icon'] }}</span>
                            <span class="text-[13px] font-medium truncate" style="color:var(--lh-text)">
                                {{ $bookmark['title'] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.app>
