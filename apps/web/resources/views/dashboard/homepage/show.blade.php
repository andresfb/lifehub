<x-layouts.app moduleName="dashboard" :modules="$modules">
    <div class="relative mx-auto max-w-7xl px-5 pt-4 pb-2 sm:px-6 sm:pt-2">
        <div class="sm:hidden">
            <x-page-actions :page-actions="$pageActions"/>
        </div>

        <div class="absolute top-6 right-6 hidden sm:block">
            <x-page-actions :page-actions="$pageActions"/>
        </div>

        {{-- Search bar --}}
        <div class="mx-auto max-w-4xl pt-3 sm:pt-8 lg:max-w-[52.08rem]">
            @php
                $storeTermAction = $searchTermActions->firstWhere('name', 'store');
                $searchTermConfig = $storeTermAction ? [
                    'url' => route($storeTermAction->route),
                    'module' => $storeTermAction->module,
                    'type' => $storeTermAction->type,
                ] : null;

                $listTermAction = $searchTermActions->firstWhere('name', 'list');
                $searchTermListUrl = $listTermAction ? route($listTermAction->route) : null;
            @endphp
            <div
                x-data="webSearch({{ Js::from($searchEngines) }}, {{ Js::from($searchTermConfig) }})"
                x-on:keydown.escape.window="isEngineDropdownOpen = false"
                id="search-wrap"
                class="card border border-base-300 bg-base-100 shadow-lg"
            >
                <div class="card-body gap-4 p-4 md:p-5">
                    <div class="relative flex flex-col gap-3 sm:flex-row sm:items-center">
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
                                x-ref="searchInput"
                                x-on:keydown.enter="doSearch()"
                                @if($listTermAction)
                                    name="query"
                                    autocomplete="off"
                                    hx-get="{{ $searchTermListUrl }}"
                                    hx-trigger="keyup[target.value.length >= 2] changed delay:300ms"
                                    hx-target="#search-suggestions"
                                    hx-swap="innerHTML"
                                    hx-vals='@json(['module' => $listTermAction->module, 'type' => $listTermAction->type], JSON_THROW_ON_ERROR)'
                                    hx-include="this"
                                    hx-on::before-request="if(this.value.length < 2) { event.preventDefault(); document.getElementById('search-suggestions').innerHTML = ''; }"
                                @endif
                                class="h-9 md:grow"
                            />
                            <button
                                type="button"
                                x-cloak
                                x-show="query.length > 0"
                                x-on:click="query = ''; document.getElementById('search-suggestions').innerHTML = ''; $refs.searchInput.focus()"
                                aria-label="Clear search"
                                class="btn btn-ghost btn-sm btn-circle text-base-content/60 hover:text-base-content"
                            >
                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <line x1="3" y1="3" x2="11" y2="11"/>
                                    <line x1="11" y1="3" x2="3" y2="11"/>
                                </svg>
                            </button>
                        </label>

                        {{-- Search button --}}
                        <button type="button" x-on:click="doSearch()" class="btn btn-primary h-8 md:h-12 px-5">
                            Search
                        </button>
                    </div>

                    @if($listTermAction)
                        <div
                            id="search-suggestions"
                            class="empty:hidden mt-1"
                            x-effect="if(query.length < 2) $el.innerHTML = ''"
                            x-on:click="const btn = $event.target.closest('.search-suggestion'); if(btn) { query = btn.dataset.term; $el.innerHTML = ''; doSearch(); }"
                        ></div>
                    @endif
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
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 lx:grid-cols-6">
                    @foreach($section->items as $bookmark)
                        <a
                            href="{{ $bookmark->url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="card w-full border border-base-300 bg-base-100 no-underline shadow-sm transition-transform duration-150 hover:-translate-y-1 hover:shadow-lg lg:w-[95%] lg:justify-self-center"
                        >
                            <div class="card-body flex-row items-center gap-3 p-4 lg:gap-[0.7125rem] lg:p-[0.95rem]">
                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-box text-base font-display font-bold text-white lg:h-9.5 lg:w-9.5 lg:text-[0.95rem]"
                                    style="background-color: {{ $bookmark->iconColor() }}"
                                >{{ $bookmark->iconName() }}</span>
                                <span class="text-sm font-medium text-base-content lg:text-[0.83125rem]">
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
