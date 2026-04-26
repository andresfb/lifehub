{{-- Command Modal --}}
{{-- Triggered by Cmd+/ (Mac) or Ctrl+/ (Win/Linux). State lives in layoutShell. --}}
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
    class="fixed inset-0 z-300 flex items-start justify-center bg-(--lh-overlay-bg) backdrop-blur-lg pt-[15vh]"
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
        class="flex w-full max-w-155 flex-col overflow-hidden rounded-2xl border border-(--lh-border) bg-(--lh-card) shadow-[0_24px_80px_rgba(0,0,0,0.3)] max-h-[60vh] mx-4"
    >
        {{-- Input row --}}
        <div class="flex h-13 shrink-0 items-center gap-2.5 border-b border-(--lh-border) px-4">
            <span class="select-none font-mono text-[15px] font-bold text-(--lh-accent)" aria-hidden="true">❯</span>
            <input
                x-ref="commandInput"
                type="text"
                placeholder="Type a command..."
                class="h-full flex-1 border-none bg-transparent font-sans text-[15px] text-(--lh-text) placeholder:text-(--lh-text-muted)"
                autocomplete="off"
                spellcheck="false"
            />
            <kbd
                class="select-none rounded-[5px] bg-(--lh-hover) px-1.75 py-0.75 font-mono text-[11px] text-(--lh-text-muted)"
                x-text="/Mac|iPhone|iPad/.test(navigator.platform) ? '⌘/' : 'Ctrl+/'"
            ></kbd>
        </div>

        {{-- Log output --}}
        <div class="min-h-30 flex-1 overflow-y-auto px-4 py-3 font-mono text-[13px] leading-[1.7]">
            <p class="text-(--lh-text-sec)">LifeHub Command — type "help" for available commands</p>
        </div>
    </div>
</div>
