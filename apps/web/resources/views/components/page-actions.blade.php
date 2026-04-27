@props([
    'pageActions' => collect(),
])

<div
    x-data="{ isOpen: false, isTooltipVisible: false }"
    x-on:keydown.escape.window="isOpen = false"
    x-on:click.outside="isOpen = false"
    {{ $attributes->class(['relative inline-flex']) }}
>
    <button
        type="button"
        x-on:click="isOpen = ! isOpen"
        x-on:mouseenter="isTooltipVisible = true"
        x-on:mouseleave="isTooltipVisible = false"
        x-on:focus="isTooltipVisible = true"
        x-on:blur="isTooltipVisible = false"
        class="flex cursor-pointer items-center gap-2 rounded-full border border-(--lh-border) bg-(--lh-card) px-3 py-2 text-(--lh-text) shadow-(--lh-shadow) transition-colors duration-150 hover:bg-(--lh-hover)"
        x-bind:aria-expanded="isOpen.toString()"
        aria-haspopup="true"
        aria-label="Open page actions"
    >
        <span class="text-[13px] font-semibold text-gray-700 sm:hidden">Manage</span>
        <span class="hidden text-3xl text-gray-500 leading-none sm:inline" aria-hidden="true">𖦏</span>
    </button>

    <span
        x-cloak
        x-bind:class="{
            'opacity-100': isTooltipVisible && ! isOpen,
            'opacity-0': ! isTooltipVisible || isOpen,
        }"
        class="pointer-events-none absolute right-0 bottom-full z-30 mb-1 hidden rounded-md bg-(--lh-text) px-2 py-1 text-[12px] font-medium whitespace-nowrap text-(--lh-bg) shadow-(--lh-shadow-lg) transition-opacity duration-150 sm:block"
        aria-hidden="true"
    >Manage</span>

    <div
        x-cloak
        x-show="isOpen"
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute top-full left-0 z-40 mt-2 min-w-52 overflow-hidden rounded-[12px] border border-(--lh-border) bg-(--lh-card) p-1 shadow-(--lh-shadow-lg) sm:right-0 sm:left-auto"
    >
        @foreach($pageActions as $pageAction)
            @if(filled($pageAction->route))
                <a
                    href="{{ resolve_route($pageAction->route) }}"
                    class="flex items-center gap-2.5 rounded-[10px] px-3 py-2 text-[13px] font-medium text-(--lh-text) no-underline transition-colors duration-150 hover:bg-(--lh-hover)"
                >
                    <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{{ $pageAction->icon }}</span>
                    <span>{{ $pageAction->label }}</span>
                </a>
            @else
                <span
                    class="flex items-center gap-2.5 rounded-[10px] px-3 py-2 text-[13px] font-medium text-(--lh-text-muted)"
                    aria-disabled="true"
                >
                    <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{{ $pageAction->icon }}</span>
                    <span>{{ $pageAction->label }}</span>
                </span>
            @endif
        @endforeach
    </div>
</div>
