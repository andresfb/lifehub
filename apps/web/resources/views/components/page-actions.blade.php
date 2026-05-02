@props([
    'pageActions' => collect(),
])

<div
    x-data="{ isOpen: false }"
    x-on:keydown.escape.window="isOpen = false"
    x-on:click.outside="isOpen = false"
    {{ $attributes->class(['tooltip tooltip-right md:tooltip-left relative inline-flex']) }}
    data-tip="Manage"
>
    <button
        type="button"
        x-on:click="isOpen = ! isOpen"
        class="btn btn-circle btn-sm sm:btn-md border-base-300 bg-base-100 shadow-sm"
        x-bind:aria-expanded="isOpen.toString()"
        aria-haspopup="true"
        aria-label="Open page actions"
    >
        <span class="text-xs font-semibold sm:hidden">Go</span>
        <span class="hidden text-xl leading-none sm:inline" aria-hidden="true">𖦏</span>
    </button>

    <div
        x-cloak
        x-show="isOpen"
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="absolute top-full left-0 z-40 mt-2 min-w-56 rounded-box border border-base-300 bg-base-100 p-2 shadow-xl sm:right-0 sm:left-auto"
    >
        <ul class="menu menu-sm w-full gap-1">
        @foreach($pageActions as $pageAction)
            @if(filled($pageAction->route))
                <li>
                    <a href="{{ resolve_route($pageAction->route) }}">
                        <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{{ $pageAction->icon }}</span>
                        <span>{{ $pageAction->label }}</span>
                    </a>
                </li>
            @else
                <li class="menu-disabled">
                    <span>
                        <span class="w-5 text-center text-lg leading-none" aria-hidden="true">{{ $pageAction->icon }}</span>
                        <span>{{ $pageAction->label }}</span>
                    </span>
                </li>
            @endif
        @endforeach
        </ul>
    </div>
</div>
