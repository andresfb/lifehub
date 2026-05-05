<x-layouts.app moduleName="dashboard" :modules="$modules">
    <div class="relative mx-auto max-w-7xl px-5 pt-4 pb-2 sm:px-6 sm:pt-2">
        <div class="sm:hidden">
            <x-page-actions :page-actions="$pageActions" />
        </div>

        <div class="absolute top-6 right-6 hidden sm:block">
            <x-page-actions :page-actions="$pageActions" />
        </div>

        {{-- Search bar --}}
        <div class="mx-auto max-w-4xl pt-3 sm:pt-8">
            <div
                x-data="webSearch({{ Js::from($searchEngines) }})"
                x-on:keydown.escape.window="isEngineDropdownOpen = false"
                id="search-wrap"
                class="card border border-base-300 bg-base-100 shadow-lg"
            >
                <div class="card-body gap-4 p-4 md:p-5">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        {{-- Engine selector --}}
                        <div class="relative shrink-0" x-on:click.outside="isEngineDropdownOpen = false">
                            <button
                                type="button"
                                x-on:click="isEngineDropdownOpen = ! isEngineDropdownOpen"
                                class="btn btn-outline justify-between gap-3 border-base-300 sm:min-w-52"
                            >
                                <span class="truncate" x-text="selectedEngine.name">{{ $searchEngines->first()->name }}</span>
                                <svg width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                                    <path d="M2.5 4L5 6.5 7.5 4"/>
                                </svg>
                            </button>

                            <div
                                x-cloak
                                x-show="isEngineDropdownOpen"
                                class="absolute top-full left-0 z-20 mt-2 min-w-full rounded-box border border-base-300 bg-base-100 p-2 shadow-lg"
                            >
                                <ul class="menu menu-sm gap-1">
                                @foreach($searchEngines as $engine)
                                    <li>
                                        <button
                                            type="button"
                                            x-on:click="selectEngine({{ Js::from($engine->toArray()) }})"
                                            x-bind:class="{ 'active': selectedEngine.name === {{ Js::from($engine->name) }} }"
                                        >
                                            <span
                                                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-box text-[10px] font-bold text-white"
                                                style="background-color: {{ $engine->iconColor() }}"
                                            >
                                                {{ $engine->iconName() }}
                                            </span>
                                            {{ $engine->name }}
                                        </button>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Search input --}}
                        <label class="input input-bordered flex h-12 flex-1 items-center gap-3">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="text-base-content/50">
                                <circle cx="7.5" cy="7.5" r="5"/>
                                <line x1="11.5" y1="11.5" x2="16" y2="16"/>
                            </svg>
                            <input
                                id="search-input"
                                type="text"
                                placeholder="Search the web..."
                                x-model="query"
                                x-on:keydown.enter="doSearch()"
                                class="h-9 md:grow"
                            />
                        </label>

                        {{-- Search button --}}
                        <button type="button" x-on:click="doSearch()" class="btn btn-primary h-8 md:h-12 px-5">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bookmarks grid --}}
    <div class="mx-auto max-w-7xl px-6 pt-6 pb-10">
        @foreach($bookmarks as $section)
            <section class="mb-8">
                <div class="mb-3 flex items-center gap-3">
                    <h3 class="text-sm font-semibold tracking-[1px] text-base-content/65 uppercase">
                    {{ $section->name }}
                    </h3>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach($section->items as $bookmark)
                        <a
                            href="{{ $bookmark->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="card border border-base-300 bg-base-100 no-underline shadow-sm transition-transform duration-150 hover:-translate-y-1 hover:shadow-lg"
                        >
                            <div class="card-body flex-row items-center gap-3 p-4">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-box text-base font-display font-bold text-white"
                                    style="background-color: {{ $bookmark->iconColor() }}"
                                >{{ $bookmark->iconName() }}</span>
                                <span class="text-sm font-medium text-base-content">
                                    {{ $bookmark->title }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>

</x-layouts.app>
