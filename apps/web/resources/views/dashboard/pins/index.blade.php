<x-layouts.app moduleName="dashboard" :modules="$data->modules">
    <div
        x-data="pinsModal({
            sections: {{ Js::from($data->sections) }},
            createRouteName: {{ Js::from($data->createRouteName) }},
            updateRouteName: {{ Js::from($data->updateRouteName) }},
        })"
        x-on:keydown.escape.window="close()"
        class="mx-auto max-w-7xl px-6 pt-5 pb-10"
    >
        <div class="mb-6 md:mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-base-content">Pin List</h1>
            </div>

            @if($data->canCreate)
                <div class="flex items-center justify-end gap-2">
                    <button type="button" class="btn btn-secondary btn-soft btn-sm border-base-300">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                        </svg>
                        New Section
                    </button>
                    <button type="button" x-on:click="openCreatePin()" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                        </svg>
                        New Pin
                    </button>
                </div>
            @endif
        </div>

        @foreach($data->bookmarks as $section)
            <section class="mb-8">
                <div class="mb-3 flex items-center gap-3">
                    <h2 class="text-base font-semibold text-base-content">{{ $section->name }}</h2>
                    <div class="badge badge-outline badge-info badge-sm">{{ $section->items->count() }} pins</div>
                </div>

                <div class="hidden overflow-hidden rounded-box border border-base-300 bg-base-100 shadow-sm md:block">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th class="w-44">Title</th>
                                    <th class="w-66">URL</th>
                                    <th class="w-[34rem]">Description</th>
                                    <th class="w-50">Tags</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section->items as $pin)
                                    <tr class="align-top">
                                        <td class="w-44 font-medium text-base-content">{{ $pin->title }}</td>
                                        <td class="w-66">
                                            <a href="{{ $pin->url }}" target="_blank" rel="noopener noreferrer" class="link link-success break-all text-sm">
                                                {{ $pin->url }}
                                            </a>
                                        </td>
                                        <td class="w-[34rem] max-w-[34rem] text-sm text-base-content/70">{{ $pin->description }}</td>
                                        <td class="w-50 max-w-44">
                                            <div class="flex max-w-44 flex-wrap gap-1.5">
                                                @forelse($pin->tags as $tag)
                                                    <span class="badge badge-soft badge-info">{{ $tag }}</span>
                                                @empty
                                                    <span class="text-xs text-base-content/50">No tags</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td>
                                            <div class="flex justify-end gap-2">
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
                                                            'tagsText' => implode(', ', $pin->tags),
                                                        ]) }})"
                                                        class="btn btn-success btn-soft btn-sm"
                                                    >
                                                        Edit
                                                    </button>
                                                @endif
                                                @if($data->canDelete)
                                                    <button type="button" class="btn btn-error btn-soft btn-sm">
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-sm text-base-content/60">No Pins in this section.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid gap-3 md:hidden">
                    @forelse($section->items as $pin)
                        <div class="card border border-base-300 bg-base-100 shadow-sm">
                            <div class="card-body gap-4 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h3 class="card-title text-base">{{ $pin->title }}</h3>
                                        <a href="{{ $pin->url }}" target="_blank" rel="noopener noreferrer" class="link link-primary break-all text-sm">
                                            {{ $pin->url }}
                                        </a>
                                    </div>
                                    <div class="badge badge-outline">{{ $pin->order }}</div>
                                </div>

                                <p class="text-sm text-base-content/70">{{ $pin->description }}</p>

                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($pin->tags as $tag)
                                        <span class="badge badge-soft badge-primary">{{ $tag }}</span>
                                    @empty
                                        <span class="text-xs text-base-content/50">No tags</span>
                                    @endforelse
                                </div>

                                <div class="card-actions justify-end">
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
                                                'tagsText' => implode(', ', $pin->tags),
                                            ]) }})"
                                            class="btn btn-outline btn-sm"
                                        >
                                            Edit
                                        </button>
                                    @endif
                                    @if($data->canDelete)
                                        <button type="button" class="btn btn-error btn-soft btn-sm">
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="alert alert-info alert-soft">No Pins in this section.</div>
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
            class="fixed inset-0 z-200 bg-base-content/35 backdrop-blur-sm"
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
                class="flex w-full max-w-4xl flex-col overflow-hidden rounded-box border border-base-300 bg-base-100 shadow-2xl"
            >
                <div class="flex items-start justify-between gap-4 border-b border-base-300 px-6 py-5">
                    <div class="min-w-0">
                        <h2 id="pin-modal-title" x-text="modalTitle" class="text-xl font-semibold text-base-content"></h2>
                        <p x-text="modalDescription" class="mt-1 text-sm text-base-content/65"></p>
                    </div>
                    <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm btn-square" aria-label="Close pin modal">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <line x1="3" y1="3" x2="11" y2="11"/><line x1="11" y1="3" x2="3" y2="11"/>
                        </svg>
                    </button>
                </div>

                <form x-bind:data-route-name="form.routeName" x-bind:data-pin-slug="form.slug" class="grid grid-cols-1 gap-4 px-6 py-5 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="pin-section-desktop" class="label px-0">
                            <span class="label-text font-medium">Section</span>
                        </label>
                        <select id="pin-section-desktop" x-model="form.sectionSlug" x-on:change="syncSectionName()" class="select select-bordered w-full">
                            <template x-for="section in sections" :key="section.slug">
                                <option :value="section.slug" x-text="section.name"></option>
                            </template>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-title-desktop" class="label px-0">
                            <span class="label-text font-medium">Title</span>
                        </label>
                        <input id="pin-title-desktop" x-ref="pinModalPrimaryInput" x-model="form.title" type="text" class="input input-bordered w-full" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-url-desktop" class="label px-0">
                            <span class="label-text font-medium">URL</span>
                        </label>
                        <input id="pin-url-desktop" x-model="form.url" type="url" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label for="pin-icon-desktop" class="label px-0">
                            <span class="label-text font-medium">Icon</span>
                        </label>
                        <input id="pin-icon-desktop" x-model="form.icon" type="text" class="input input-bordered w-full" />
                    </div>

                    <div>
                        <label for="pin-icon-color-desktop" class="label px-0">
                            <span class="label-text font-medium">Icon Color</span>
                        </label>
                        <input id="pin-icon-color-desktop" x-model="form.iconColor" type="text" class="input input-bordered w-full" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-description-desktop" class="label px-0">
                            <span class="label-text font-medium">Description</span>
                        </label>
                        <textarea id="pin-description-desktop" x-model="form.description" rows="4" class="textarea textarea-bordered w-full"></textarea>
                    </div>

                    <div class="md:col-span-2" x-show="mode === 'edit'">
                        <label for="pin-order-desktop" class="label px-0">
                            <span class="label-text font-medium">Order</span>
                        </label>
                        <input id="pin-order-desktop" x-model="form.order" type="text" class="input input-bordered w-full" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="pin-tags-desktop" class="label px-0">
                            <span class="label-text font-medium">Tags</span>
                        </label>
                        <input id="pin-tags-desktop" x-model="form.tagsText" type="text" class="input input-bordered w-full" />
                    </div>
                </form>

                <div class="flex items-center justify-end border-t border-base-300 bg-base-200/60 px-6 py-4 text-xs text-base-content/60">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">Cancel</button>
                        <button type="button" class="btn btn-primary btn-sm">
                            <span x-text="mode === 'edit' ? 'Update Pin' : 'Create Pin'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            x-cloak
            x-bind:class="{ 'translate-y-full': ! isOpen }"
            class="fixed inset-x-0 bottom-0 z-300 flex max-h-[85vh] w-screen flex-col overflow-hidden rounded-t-box border-t border-base-300 bg-base-100 shadow-2xl transition-transform duration-250 ease-in-out md:hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pin-modal-title-mobile"
        >
            <div class="flex items-start justify-between gap-4 border-b border-base-300 px-5 py-4">
                <div class="min-w-0">
                    <h2 id="pin-modal-title-mobile" x-text="modalTitle" class="text-lg font-semibold text-base-content"></h2>
                    <p x-text="modalDescription" class="mt-1 text-sm text-base-content/65"></p>
                </div>
                <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm btn-square" aria-label="Close pin modal">
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                        <line x1="3" y1="3" x2="11" y2="11"/><line x1="11" y1="3" x2="3" y2="11"/>
                    </svg>
                </button>
            </div>

            <form x-bind:data-route-name="form.routeName" x-bind:data-pin-slug="form.slug" class="grid grid-cols-1 gap-4 overflow-y-auto px-5 py-4">
                <div>
                    <label for="pin-section-mobile" class="label px-0">
                        <span class="label-text font-medium">Section</span>
                    </label>
                    <select id="pin-section-mobile" x-model="form.sectionSlug" x-on:change="syncSectionName()" class="select select-bordered w-full">
                        <template x-for="section in sections" :key="section.slug">
                            <option :value="section.slug" x-text="section.name"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label for="pin-title-mobile" class="label px-0">
                        <span class="label-text font-medium">Title</span>
                    </label>
                    <input id="pin-title-mobile" x-model="form.title" type="text" class="input input-bordered w-full" />
                </div>

                <div>
                    <label for="pin-url-mobile" class="label px-0">
                        <span class="label-text font-medium">URL</span>
                    </label>
                    <input id="pin-url-mobile" x-model="form.url" type="url" class="input input-bordered w-full" />
                </div>

                <div>
                    <label for="pin-icon-mobile" class="label px-0">
                        <span class="label-text font-medium">Icon</span>
                    </label>
                    <input id="pin-icon-mobile" x-model="form.icon" type="text" class="input input-bordered w-full" />
                </div>

                <div>
                    <label for="pin-icon-color-mobile" class="label px-0">
                        <span class="label-text font-medium">Icon Color</span>
                    </label>
                    <input id="pin-icon-color-mobile" x-model="form.iconColor" type="text" class="input input-bordered w-full" />
                </div>

                <div>
                    <label for="pin-description-mobile" class="label px-0">
                        <span class="label-text font-medium">Description</span>
                    </label>
                    <textarea id="pin-description-mobile" x-model="form.description" rows="4" class="textarea textarea-bordered w-full"></textarea>
                </div>

                <div x-show="mode === 'edit'">
                    <label for="pin-order-mobile" class="label px-0">
                        <span class="label-text font-medium">Order</span>
                    </label>
                    <input id="pin-order-mobile" x-model="form.order" type="text" class="input input-bordered w-full" />
                </div>

                <div>
                    <label for="pin-tags-mobile" class="label px-0">
                        <span class="label-text font-medium">Tags</span>
                    </label>
                    <input id="pin-tags-mobile" x-model="form.tagsText" type="text" class="input input-bordered w-full" />
                </div>
            </form>

            <div class="flex items-center justify-end border-t border-base-300 bg-base-200/60 px-5 py-4 text-xs text-base-content/60">
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="button" class="btn btn-primary btn-sm">
                        <span x-text="mode === 'edit' ? 'Update Pin' : 'Create Pin'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
