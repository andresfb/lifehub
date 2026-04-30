<x-layouts.app moduleName="dashboard" :modules="$modules">
    <div class="mx-auto max-w-330 px-6 pt-5 pb-10">

        <h1 class="mb-5 text-[22px] font-bold tracking-[-0.3px] text-(--lh-text)">Pin List</h1>

        @if($canCreate)
            <div class="mb-5 flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[13px] font-semibold text-(--lh-text) shadow-(--lh-shadow) transition-colors duration-150 hover:bg-(--lh-hover)"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                    </svg>
                    New Section
                </button>
                <button
                    type="button"
                    class="inline-flex cursor-pointer items-center gap-2 rounded-[10px] border border-(--lh-border) bg-(--lh-card) px-3.5 py-2 text-[13px] font-semibold text-(--lh-text) shadow-(--lh-shadow) transition-colors duration-150 hover:bg-(--lh-hover)"
                >
                    <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                        <line x1="7" y1="1" x2="7" y2="13"/><line x1="1" y1="7" x2="13" y2="7"/>
                    </svg>
                    New Pin
                </button>
            </div>
        @endif

        @foreach($bookmarks as $section)
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
                                @if($canEdit)
                                    <button
                                        type="button"
                                        class="cursor-pointer rounded-md border border-(--lh-border) bg-(--lh-card) px-2.5 py-1 text-[12px] font-medium text-(--lh-text) transition-colors duration-150 hover:bg-(--lh-hover)"
                                    >
                                        Edit
                                    </button>
                                @endif
                                @if($canDelete)
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

    </div>
</x-layouts.app>
