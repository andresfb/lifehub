<x-layouts.app moduleName="dashboard" :modules="$modules">
    {{-- Search bar --}}
    <div class="mx-auto max-w-160 px-5 pt-8 pb-2">
        <div
            x-data="webSearch({{ Js::from($searchEngines) }})"
            x-on:keydown.escape.window="isEngineDropdownOpen = false"
            id="search-wrap"
            @class([
                'flex',
                'h-12',
                'items-center',
                'rounded-[14px]',
                'border-2',
                'border-(--lh-border)',
                'bg-(--lh-input)',
                'shadow-(--lh-shadow)',
                'transition-[border-color,box-shadow]',
                'duration-200',
                'focus-within:border-(--lh-accent)',
                'focus-within:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.13)]',
            ])
        >
            {{-- Engine selector --}}
            <div class="relative shrink-0" x-on:click.outside="isEngineDropdownOpen = false">
                <button
                    type="button"
                    x-on:click="isEngineDropdownOpen = ! isEngineDropdownOpen"
                    class="flex h-12 cursor-pointer items-center gap-1.5 border-none bg-transparent pr-1 pl-3.5 text-[13px] font-medium text-(--lh-text-sec)"
                >
                    <span x-text="selectedEngine.name">{{ $searchEngines[0]['name'] }}</span>
                    <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                        <path d="M2.5 4L5 6.5 7.5 4"/>
                    </svg>
                </button>

                <div
                    x-cloak
                    x-show="isEngineDropdownOpen"
                    class="absolute top-full left-2 z-20 mt-1 min-w-42.5 rounded-[10px] border border-(--lh-border) bg-(--lh-card) p-1 shadow-(--lh-shadow-lg)"
                >
                    @foreach($searchEngines as $engine)
                        <button
                            type="button"
                            x-on:click="selectEngine({{ Js::from($engine) }})"
                            x-bind:class="{ 'bg-(--lh-accent-light)': selectedEngine.name === {{ Js::from($engine['name']) }} }"
                            class="flex w-full cursor-pointer items-center gap-2 rounded-md border-none px-2.5 py-2 text-left text-[13px] text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                        >
                            <span
                                class="flex h-5 w-5 shrink-0 items-center justify-center rounded-[5px] text-[10px] font-bold text-white {{ $engine['colorClass'] }}">{{ $engine['icon'] }}</span>
                            {{ $engine['name'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Divider --}}
            <div class="h-6 w-px shrink-0 bg-(--lh-border)"></div>

            {{-- Search input --}}
            <input
                id="search-input"
                type="text"
                placeholder="Search the web..."
                x-model="query"
                x-on:keydown.enter="doSearch()"
                class="h-full flex-1 border-none bg-transparent px-3.5 text-[15px] text-(--lh-text)"
            />

            {{-- Search button --}}
            <button
                type="button"
                x-on:click="doSearch()"
                class="flex h-full cursor-pointer items-center border-none bg-transparent px-4 text-(--lh-text-muted) transition-colors duration-150 hover:text-(--lh-accent)"
            >
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <circle cx="7.5" cy="7.5" r="5"/>
                    <line x1="11.5" y1="11.5" x2="16" y2="16"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Bookmarks grid --}}
    <div class="mx-auto max-w-330 px-6 pt-5 pb-10">
        @foreach($bookmarks as $section)
            <div class="mb-7">
                <h3 class="mb-2.5 pl-1 text-[12px] font-semibold tracking-[1px] text-(--lh-text-muted) uppercase">
                    {{ $section->name }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-2 md:gap-4 lg:gap-6">
                    @foreach($section->items as $bookmark)
                        <a
                            href="{{ $bookmark->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            @class([
                                'flex',
                                'cursor-pointer',
                                'items-center',
                                'gap-2.5',
                                'rounded-[10px]',
                                'border',
                                'border-(--lh-border-light)',
                                'bg-(--lh-card)',
                                'px-3.5',
                                'py-3',
                                'no-underline',
                                'transition-[background-color,border-color,transform]',
                                'duration-150',
                                'hover:-translate-y-px',
                                'hover:border-(--lh-border)',
                                'hover:bg-(--lh-hover)',
                            ])
                        >
                            <span
                                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-display text-[14px] font-bold text-white"
                                style="background-color: {{ $bookmark->iconColor() }}"
                            >{{ $bookmark->iconName() }}</span>
                            <span class="text-[13px] font-medium text-(--lh-text)">
                                {{ $bookmark->title }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

</x-layouts.app>
