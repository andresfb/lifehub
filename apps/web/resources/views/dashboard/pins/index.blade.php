<x-layouts.app moduleName="dashboard" :modules="$data->modules">
    <div
        x-data="pinsModal({
            sections: {{ Js::from($data->sections) }},
            createRouteName: {{ Js::from($data->createRouteName) }},
            updateRouteName: {{ Js::from($data->updateRouteName) }},
        })"
        x-on:keydown.escape.window="close()"
        class="mx-auto max-w-330 px-6 pt-5 pb-10"
    >

        <h1 class="mb-5 text-[22px] font-bold tracking-[-0.3px] text-(--lh-text)">Pin List</h1>

        @if($data->canCreate)
            <div class="mb-2 flex items-center justify-end gap-2 md:mb-0">
                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[12px] font-semibold text-(--lh-text) shadow-(--lh-shadow) transition-colors duration-150 hover:bg-(--lh-hover)"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                    </svg>
                    New Section
                </button>
                <button
                    type="button"
                    x-on:click="openCreatePin()"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[12px] font-semibold text-(--lh-text) shadow-(--lh-shadow) transition-colors duration-150 hover:bg-(--lh-hover)"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                    </svg>
                    New Pin
                </button>
            </div>
        @endif

        @foreach($data->bookmarks as $section)
            <section class="mb-7">
                <h3 class="mb-2.5 pl-1 text-[12px] font-semibold tracking-[1px] text-(--lh-text-muted) uppercase">
                    {{ $section->name }}
                </h3>

                <div class="overflow-hidden rounded-[10px] border border-(--lh-border-light) bg-(--lh-card)">

                    {{-- Header row (md+) --}}
                    <div class="hidden md:grid md:grid-cols-[minmax(0,2fr)_minmax(0,2fr)_minmax(0,3fr)_minmax(0,2fr)_8rem] gap-3 border-b border-(--lh-border-light) bg-(--lh-hover) px-4 py-2 text-[11px] font-semibold tracking-[0.5px] text-(--lh-text-muted) uppercase">
                        <div>Title</div>
                        <div>URL</div>
                        <div>Description</div>
                        <div>Tags</div>
                        <div class="text-center">Actions</div>
                    </div>

                    @forelse($section->items as $pin)
                        <div class="grid grid-cols-1 gap-y-1 border-b border-(--lh-border-light) px-4 py-3 last:border-b-0 transition-colors duration-150 hover:bg-(--lh-hover) md:grid-cols-[minmax(0,2fr)_minmax(0,2fr)_minmax(0,3fr)_minmax(0,2fr)_8rem] md:items-start md:gap-3 md:gap-y-0">

                            {{-- Title --}}
                            <div class="min-w-0">
                                <span class="mr-1.5 text-[11px] font-semibold tracking-[0.5px] text-(--lh-text-muted) uppercase md:hidden">Title:</span>
                                <span class="break-words text-[13px] font-medium text-(--lh-text)">{{ $pin->title }}</span>
                            </div>

                            {{-- URL --}}
                            <div class="min-w-0">
                                <span class="mr-1.5 text-[11px] font-semibold tracking-[0.5px] text-(--lh-text-muted) uppercase md:hidden">URL:</span>
                                <a
                                    href="{{ $pin->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="break-all text-[13px] text-(--lh-accent) no-underline hover:underline"
                                >{{ $pin->url }}</a>
                            </div>

                            {{-- Description --}}
                            <div class="break-words text-[13px] whitespace-normal text-(--lh-text-sec)">
                                <span class="mr-1.5 text-[11px] font-semibold tracking-[0.5px] text-(--lh-text-muted) uppercase md:hidden">Description:</span>{{ $pin->description }}
                            </div>

                            {{-- Tags --}}
                            <div class="break-words text-[13px] whitespace-normal text-(--lh-text-muted)">
                                <span class="mr-1.5 text-[11px] font-semibold tracking-[0.5px] uppercase md:hidden">Tags:</span>{{ implode(', ', $pin->tags) }}
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 md:justify-end">
                                @if($data->canEdit)
                                    <button
                                        type="button"
                                        x-on:click="openEditPin({{ Js::from([
                                            'slug' => $pin->slug,
                                            'sectionSlug' => $section->slug,
                                            'sectionName' => $section->name,
                                            'title' => $pin->title,
                                            'url' => $pin->url,
                                            'order' => $pin->order,
                                            'icon' => $pin->icon,
                                            'iconColor' => $pin->icon_color,
                                            'description' => $pin->description,
                                            'tagsText' => implode(", ", $pin->tags)
                                        ]) }})"
                                        class="cursor-pointer rounded-md border border-(--lh-border) bg-(--lh-card) px-2.5 py-1 text-[12px] font-medium text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                                    >
                                        Edit
                                    </button>
                                @endif
                                @if($data->canDelete)
                                    <button
                                        type="button"
                                        class="cursor-pointer rounded-md border border-(--lh-border) bg-(--lh-card) px-2.5 py-1 text-[12px] font-medium text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                                    >
                                        Delete
                                    </button>
                                @endif
                            </div>

                        </div>
                    @empty
                        <div class="px-4 py-6 text-center text-[13px] text-(--lh-text-muted)">
                            No Pins in this section.
                        </div>
                    @endforelse

                </div>
            </section>
        @endforeach

        <div
            x-cloak
            x-show="isOpen"
            x-on:click="close()"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-200 bg-(--lh-overlay-bg) backdrop-blur-md"
            aria-hidden="true"
        ></div>

        <div
            x-cloak
            x-show="isOpen"
            x-on:click="close()"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-300 hidden items-start justify-center px-4 pt-[10vh] md:flex"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pin-modal-title"
        >
            <div
                x-on:click.stop
                x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition duration-100 ease-in"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="flex w-full max-w-3xl flex-col overflow-hidden rounded-[18px] border border-(--lh-border) bg-(--lh-card) shadow-[0_24px_80px_rgba(0,0,0,0.3)]"
            >
                <div class="flex items-start justify-between gap-4 border-b border-(--lh-border-light) px-6 py-5">
                    <div class="min-w-0">
                        <h2 id="pin-modal-title" x-text="modalTitle" class="text-[18px] font-semibold tracking-[-0.2px] text-(--lh-text)"></h2>
                        <p x-text="modalDescription" class="mt-1 text-[13px] text-(--lh-text-muted)"></p>
                    </div>
                    <button
                        type="button"
                        x-on:click="close()"
                        class="flex cursor-pointer rounded-md border border-(--lh-border) bg-(--lh-card) p-2 text-(--lh-text-muted) transition-colors duration-150 hover:bg-(--lh-hover) hover:text-(--lh-text)"
                        aria-label="Close pin modal"
                    >
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <line x1="3" y1="3" x2="11" y2="11"/><line x1="11" y1="3" x2="3" y2="11"/>
                        </svg>
                    </button>
                </div>

                <form
                    x-bind:data-route-name="form.routeName"
                    x-bind:data-pin-slug="form.slug"
                    class="grid grid-cols-1 gap-4 px-6 py-5 md:grid-cols-2"
                >
                    <div class="md:col-span-2">
                        <label for="pin-section-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Section
                        </label>
                        <select
                            id="pin-section-desktop"
                            x-model="form.sectionSlug"
                            x-on:change="syncSectionName()"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        >
                            <template x-for="section in sections" :key="section.slug">
                                <option :value="section.slug" x-text="section.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label for="pin-title-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Title
                        </label>
                        <input
                            id="pin-title-desktop"
                            x-ref="pinModalPrimaryInput"
                            x-model="form.title"
                            type="text"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>

                    <div>
                        <label for="pin-order-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Order
                        </label>
                        <input
                            id="pin-order-desktop"
                            x-model="form.order"
                            type="text"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-url-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            URL
                        </label>
                        <input
                            id="pin-url-desktop"
                            x-model="form.url"
                            type="url"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>

                    <div>
                        <label for="pin-icon-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Icon
                        </label>
                        <input
                            id="pin-icon-desktop"
                            x-model="form.icon"
                            type="text"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>

                    <div>
                        <label for="pin-icon-color-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Icon Color
                        </label>
                        <input
                            id="pin-icon-color-desktop"
                            x-model="form.iconColor"
                            type="text"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-description-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Description
                        </label>
                        <textarea
                            id="pin-description-desktop"
                            x-model="form.description"
                            rows="4"
                            class="w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 py-3 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        ></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-tags-desktop" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                            Tags
                        </label>
                        <input
                            id="pin-tags-desktop"
                            x-model="form.tagsText"
                            type="text"
                            class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                        />
                    </div>
                </form>

                <div class="flex items-center justify-between gap-3 border-t border-(--lh-border-light) px-6 py-4">
                    <div class="text-[12px] text-(--lh-text-muted)">
                        Route:
                        <span x-text="form.routeName" class="font-medium text-(--lh-text-sec)"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            x-on:click="close()"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[12px] font-semibold text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border-none bg-(--lh-accent) px-3.5 py-2 text-[12px] font-semibold text-white transition-[opacity,transform] duration-150 hover:opacity-90 active:scale-[0.98]"
                        >
                            <span x-text="mode === 'edit' ? 'Update Pin' : 'Create Pin'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-cloak
            x-bind:class="{ 'translate-y-full': ! isOpen }"
            class="fixed inset-x-0 bottom-0 z-300 flex max-h-[85vh] w-screen flex-col overflow-hidden rounded-t-[18px] border-t border-(--lh-border) bg-(--lh-card) shadow-[0_-12px_40px_rgba(0,0,0,0.3)] transition-transform duration-250 ease-in-out md:hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pin-modal-title-mobile"
        >
            <div class="flex items-start justify-between gap-4 border-b border-(--lh-border-light) px-5 py-4">
                <div class="min-w-0">
                    <h2 id="pin-modal-title-mobile" x-text="modalTitle" class="text-[17px] font-semibold tracking-[-0.2px] text-(--lh-text)"></h2>
                    <p x-text="modalDescription" class="mt-1 text-[13px] text-(--lh-text-muted)"></p>
                </div>
                <button
                    type="button"
                    x-on:click="close()"
                    class="flex cursor-pointer rounded-md border border-(--lh-border) bg-(--lh-card) p-2 text-(--lh-text-muted) transition-colors duration-150 hover:bg-(--lh-hover) hover:text-(--lh-text)"
                    aria-label="Close pin modal"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <line x1="3" y1="3" x2="11" y2="11"/><line x1="11" y1="3" x2="3" y2="11"/>
                    </svg>
                </button>
            </div>

            <form
                x-bind:data-route-name="form.routeName"
                x-bind:data-pin-slug="form.slug"
                class="grid grid-cols-1 gap-4 overflow-y-auto px-5 py-4"
            >
                <div>
                    <label for="pin-section-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Section
                    </label>
                    <select
                        id="pin-section-mobile"
                        x-model="form.sectionSlug"
                        x-on:change="syncSectionName()"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    >
                        <template x-for="section in sections" :key="section.slug">
                            <option :value="section.slug" x-text="section.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="pin-title-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Title
                    </label>
                    <input
                        id="pin-title-mobile"
                        x-model="form.title"
                        type="text"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>

                <div>
                    <label for="pin-order-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Order
                    </label>
                    <input
                        id="pin-order-mobile"
                        x-model="form.order"
                        type="text"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>

                <div>
                    <label for="pin-url-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        URL
                    </label>
                    <input
                        id="pin-url-mobile"
                        x-model="form.url"
                        type="url"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>

                <div>
                    <label for="pin-icon-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Icon
                    </label>
                    <input
                        id="pin-icon-mobile"
                        x-model="form.icon"
                        type="text"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>

                <div>
                    <label for="pin-icon-color-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Icon Color
                    </label>
                    <input
                        id="pin-icon-color-mobile"
                        x-model="form.iconColor"
                        type="text"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>

                <div>
                    <label for="pin-description-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Description
                    </label>
                    <textarea
                        id="pin-description-mobile"
                        x-model="form.description"
                        rows="4"
                        class="w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 py-3 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    ></textarea>
                </div>

                <div>
                    <label for="pin-tags-mobile" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                        Tags
                    </label>
                    <input
                        id="pin-tags-mobile"
                        x-model="form.tagsText"
                        type="text"
                        class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                    />
                </div>
            </form>

            <div class="flex items-center justify-between gap-3 border-t border-(--lh-border-light) px-5 py-4">
                <div class="text-[12px] text-(--lh-text-muted)">
                    Route:
                    <span x-text="form.routeName" class="font-medium text-(--lh-text-sec)"></span>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        x-on:click="close()"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[12px] font-semibold text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border-none bg-(--lh-accent) px-3.5 py-2 text-[12px] font-semibold text-white transition-[opacity,transform] duration-150 hover:opacity-90 active:scale-[0.98]"
                    >
                        <span x-text="mode === 'edit' ? 'Update Pin' : 'Create Pin'"></span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
