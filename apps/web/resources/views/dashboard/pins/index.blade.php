<x-layouts.app moduleName="dashboard" :modules="$data->modules">
    <div
        x-data="pinsModal({
            sections: {{ Js::from($data->sections) }},
            storeAction: {{ Js::from($data->storeAction) }},
            updateAction: {{ Js::from($data->updateAction) }},
        })"
        x-on:keydown.escape.window="close()"
        class="mx-auto max-w-7xl px-6 pt-5 pb-10"
    >
        <div class="mb-6 flex flex-col gap-4 md:mb-8 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-base-content">Pin List</h1>
            </div>

            @if($data->canCreate)
                <div class="flex items-center justify-end gap-2">
                    <button type="button" x-on:click="openCreatePin()" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                            <line x1="7" y1="1" x2="7" y2="13"/>
                            <line x1="1" y1="7" x2="13" y2="7"/>
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
                                <th class="w-136">Description</th>
                                <th class="w-50">Tags</th>
                                <th class="w-28">Active</th>
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
                                    <td class="w-136 max-w-136 text-sm text-base-content/70">{{ $pin->description }}</td>
                                    <td class="w-50 max-w-44">
                                        <div class="flex max-w-44 flex-wrap gap-1.5">
                                            @forelse($pin->tags as $tag)
                                                <span class="badge badge-soft badge-info">{{ $tag }}</span>
                                            @empty
                                                <span class="text-xs text-base-content/50">No tags</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="w-28">
                                        <span @class([
                                            'badge badge-sm',
                                            'badge-secondary badge-soft' => $pin->active,
                                            'badge-ghost' => ! $pin->active,
                                        ])>
                                            {{ $pin->active ? 'Active' : 'Inactive' }}
                                        </span>
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
                                                        'active' => $pin->active,
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
                                                <button
                                                    type="button"
                                                    x-on:click="openDeleteConfirmation({{ Js::from([
                                                        'action' => route($data->deleteRouteName, ['pin' => $pin->slug]),
                                                        'title' => $pin->title,
                                                        'sectionName' => $section->name,
                                                    ]) }})"
                                                    class="btn btn-error btn-soft btn-sm"
                                                >
                                                    Delete
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-sm text-base-content/60">No Pins in this section.</td>
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
                                        <a href="{{ $pin->url }}" target="_blank" rel="noopener noreferrer" class="mt-2 link link-success break-all text-sm">
                                            {{ $pin->url }}
                                        </a>
                                    </div>
                                    <div class="badge badge-outline">{{ $pin->order }}</div>
                                </div>

                                <p class="text-sm text-base-content/70">{{ $pin->description }}</p>

                                <div class="flex flex-wrap gap-1.5">
                                    @forelse($pin->tags as $tag)
                                        <span class="badge badge-soft badge-info">{{ $tag }}</span>
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
                                                'active' => $pin->active,
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
                                        <button
                                            type="button"
                                            x-on:click="openDeleteConfirmation({{ Js::from([
                                                'action' => route($data->deleteRouteName, ['pin' => $pin->slug]),
                                                'title' => $pin->title,
                                                'sectionName' => $section->name,
                                            ]) }})"
                                            class="btn btn-error btn-soft btn-sm"
                                        >
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
            x-show="isOpen || isDeleteConfirmationOpen"
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
            x-show="isDeleteConfirmationOpen"
            x-on:click="close()"
            x-transition:enter="transition duration-150 ease-out"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition duration-100 ease-in"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-300 hidden items-start justify-center px-4 pt-[18vh] md:flex"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pin-delete-modal-title"
        >
            <div
                x-on:click.stop
                x-transition:enter="transition duration-150 ease-out"
                x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition duration-100 ease-in"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                class="flex w-full max-w-xl flex-col overflow-hidden rounded-box border border-error/20 bg-base-100 shadow-2xl"
            >
                <div class="border-b border-base-300 px-6 py-5">
                    <div class="flex items-start gap-4">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-error/12 text-error">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M7 3h4"/>
                                <path d="M4 5h10"/>
                                <path d="M6 5v8a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V5"/>
                                <path d="M8 8v4"/>
                                <path d="M10 8v4"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 id="pin-delete-modal-title" class="text-xl font-semibold text-base-content">Delete pin?</h2>
                            <p x-text="deleteConfirmationDescription" class="mt-1 text-sm text-base-content/70"></p>
                        </div>
                        <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm btn-square" aria-label="Close delete confirmation">
                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                                <line x1="3" y1="3" x2="11" y2="11"/>
                                <line x1="11" y1="3" x2="3" y2="11"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <form id="pin-delete-form-desktop" method="POST" x-bind:action="deleteConfirmation.action" class="px-6 py-5">
                    @csrf
                    @method('DELETE')
                    <div class="text-neutral font-medium">This can't be undone.</div>
                </form>

                <div class="flex items-center justify-end gap-2 border-t border-base-300 bg-base-200/60 px-6 py-4">
                    <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">No</button>
                    <button type="submit" form="pin-delete-form-desktop" class="btn btn-error btn-sm">Yes</button>
                </div>
            </div>
        </div>

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
                            <line x1="3" y1="3" x2="11" y2="11"/>
                            <line x1="11" y1="3" x2="3" y2="11"/>
                        </svg>
                    </button>
                </div>

                <x-dashboard.pins.form
                    form-id="pin-form-desktop"
                    viewport="desktop"
                    form-class="grid grid-cols-1 gap-4 px-6 py-5 md:grid-cols-2"
                    section-wrapper-class="md:col-span-2"
                    title-wrapper-class="md:col-span-2"
                    url-wrapper-class="md:col-span-2"
                    description-wrapper-class="md:col-span-2"
                    edit-group-class="grid gap-6 md:col-span-2 md:grid-cols-2"
                    tags-wrapper-class="md:col-span-2"
                    title-input-ref="pinModalPrimaryInput"
                    tag-key-prefix="desktop-tag"
                />

                <div class="flex items-center justify-end border-t border-base-300 bg-base-200/60 px-6 py-4 text-xs text-base-content/60">
                    <div class="flex items-center gap-2">
                        <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">Cancel</button>
                        <button type="submit" form="pin-form-desktop" class="btn btn-primary btn-sm">
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
                        <line x1="3" y1="3" x2="11" y2="11"/>
                        <line x1="11" y1="3" x2="3" y2="11"/>
                    </svg>
                </button>
            </div>

            <x-dashboard.pins.form
                form-id="pin-form-mobile"
                viewport="mobile"
                form-class="grid grid-cols-1 gap-4 overflow-y-auto px-5 py-4"
                edit-group-class="grid gap-4"
                tag-key-prefix="mobile-tag"
            />

            <div class="flex items-center justify-end border-t border-base-300 bg-base-200/60 px-5 py-4 text-xs text-base-content/60">
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">Cancel</button>
                    <button type="submit" form="pin-form-mobile" class="btn btn-primary btn-sm">
                        <span x-text="mode === 'edit' ? 'Update Pin' : 'Create Pin'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div
            x-cloak
            x-bind:class="{ 'translate-y-full': ! isDeleteConfirmationOpen }"
            class="fixed inset-x-0 bottom-0 z-300 flex w-screen flex-col overflow-hidden rounded-t-box border-t border-base-300 bg-base-100 shadow-2xl transition-transform duration-250 ease-in-out md:hidden"
            role="dialog"
            aria-modal="true"
            aria-labelledby="pin-delete-modal-title-mobile"
        >
            <div class="border-b border-base-300 px-5 py-4">
                <div class="flex items-start gap-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-error/12 text-error">
                        <svg width="16" height="16" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M7 3h4"/>
                            <path d="M4 5h10"/>
                            <path d="M6 5v8a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V5"/>
                            <path d="M8 8v4"/>
                            <path d="M10 8v4"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <h2 id="pin-delete-modal-title-mobile" class="text-lg font-semibold text-base-content">Delete pin?</h2>
                        <p x-text="deleteConfirmationDescription" class="mt-1 text-sm text-base-content/70"></p>
                    </div>
                    <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm btn-square" aria-label="Close delete confirmation">
                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true">
                            <line x1="3" y1="3" x2="11" y2="11"/>
                            <line x1="11" y1="3" x2="3" y2="11"/>
                        </svg>
                    </button>
                </div>
            </div>

            <form id="pin-delete-form-mobile" method="POST" x-bind:action="deleteConfirmation.action" class="px-5 py-4">
                @csrf
                @method('DELETE')
                <div class="text-neutral font-medium">This can't be undone.</div>
            </form>

            <div class="flex items-center justify-end gap-2 border-t border-base-300 bg-base-200/60 px-5 py-4">
                <button type="button" x-on:click="close()" class="btn btn-ghost btn-sm">No</button>
                <button type="submit" form="pin-delete-form-mobile" class="btn btn-error btn-sm">Yes</button>
            </div>
        </div>
    </div>
</x-layouts.app>
