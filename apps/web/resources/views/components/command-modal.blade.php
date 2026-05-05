{{-- Command UI --}}
{{-- Triggered by Cmd+/ (Mac) or Ctrl+/ (Win/Linux). State lives in layoutShell. --}}
{{-- Desktop (md+) renders a centered modal; mobile (sm and below) renders a bottom sheet. --}}

{{-- Desktop modal --}}
<div
    x-cloak
    x-show="isCommandOpen"
    x-on:click="isCommandOpen = false"
    x-transition:enter="transition duration-150 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition duration-100 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-300 hidden items-start justify-center bg-base-content/30 pt-[15vh] backdrop-blur-lg md:flex"
    role="dialog"
    aria-modal="true"
    aria-label="Command window"
>
    <div
        x-on:click.stop
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        @class([
            'flex',
            'w-full',
            'max-w-[42.5rem]',
            'mx-4',
            'h-[30vh]',
            'flex-col',
            'overflow-hidden',
            'rounded-box',
            'border',
            'border-base-300',
            'bg-base-100',
            'shadow-2xl',
        ])
    >
        <x-command-input inputName="commandInputDesktop" />

    </div>
</div>

{{-- Mobile bottom-sheet backdrop --}}
<div
    x-cloak
    x-show="isCommandOpen"
    x-on:click="isCommandOpen = false"
    x-transition:enter="transition duration-200 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed top-0 left-0 z-200 h-screen w-screen bg-base-content/30 md:hidden"
    aria-hidden="true"
></div>

{{-- Mobile bottom sheet: always mounted, slides up via transform like the side menu --}}
<div
    x-cloak
    x-bind:class="{ 'translate-y-full': ! isCommandOpen }"
    @class([
        'md:hidden',
        'fixed',
        'bottom-0',
        'inset-x-0',
        'w-screen',
        'z-300',
        'flex',
        'h-[75vh]',
        'flex-col',
        'overflow-hidden',
        'border-t',
        'border-base-300',
        'bg-base-100',
        'shadow-2xl',
        'transition-transform',
        'duration-250',
        'ease-in-out',
    ])
    role="dialog"
    aria-modal="true"
    aria-label="Command window"
>
    <x-command-input inputName="commandInputMobile" />

</div>
