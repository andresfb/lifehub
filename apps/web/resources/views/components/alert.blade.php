@props([
    'type' => 'success',
    'message',
    'dismissAfter' => 10000,
])

@php
    $styles = match ($type) {
        'warning' => [
            'classes' => 'alert alert-warning alert-soft',
            'icon' => 'warning',
        ],
        'error' => [
            'classes' => 'alert alert-error alert-soft',
            'icon' => 'error',
        ],
        default => [
            'classes' => 'alert alert-success alert-soft',
            'icon' => 'success',
        ],
    };
@endphp

<div
    x-data="dismissibleAlert({ timeout: {{ (int) $dismissAfter }} })"
    x-cloak
    x-show="isVisible"
    x-transition.opacity.duration.200ms
    {{ $attributes->class([$styles['classes'], 'items-start gap-3 text-sm sm:items-center']) }}
    role="alert"
>
    <span class="mt-0.5 shrink-0 sm:mt-0" aria-hidden="true">
        @switch($styles['icon'])
            @case('warning')
                <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10 3.5 17 16.5H3L10 3.5Z" />
                    <path d="M10 7.5v4.5" />
                    <circle cx="10" cy="14.25" r=".75" fill="currentColor" stroke="none" />
                </svg>
                @break
            @case('error')
                <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="10" cy="10" r="7" />
                    <path d="m7.5 7.5 5 5" />
                    <path d="m12.5 7.5-5 5" />
                </svg>
                @break
            @default
                <svg class="size-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="10" cy="10" r="7" />
                    <path d="m7 10 2 2 4-4" />
                </svg>
        @endswitch
    </span>

    <span class="min-w-0 flex-1 leading-6">{{ $message }}</span>

    <button
        type="button"
        x-on:click="dismiss()"
        class="btn btn-ghost btn-xs btn-circle shrink-0"
        aria-label="Dismiss alert"
    >
        <svg class="size-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
            <path d="M4 4l8 8" />
            <path d="M12 4 4 12" />
        </svg>
    </button>
</div>
