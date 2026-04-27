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
    class="hidden md:flex fixed inset-0 z-300 items-start justify-center bg-(--lh-overlay-bg) backdrop-blur-lg pt-[15vh]"
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
        class="flex w-full max-w-[42.5rem] mx-4 max-h-[72vh] flex-col overflow-hidden rounded-2xl border border-(--lh-border) bg-(--lh-card) shadow-[0_24px_80px_rgba(0,0,0,0.3)]"
    >
        {{-- Input row --}}
        <div class="flex h-13 shrink-0 items-center gap-2.5 border-b border-(--lh-border) px-4">
            <span class="select-none font-mono text-[15px] font-bold text-(--lh-accent)" aria-hidden="true">❯</span>
            <input
                x-ref="commandInputDesktop"
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
    class="md:hidden fixed top-0 left-0 w-screen h-screen z-200 bg-(--lh-overlay-bg)"
    aria-hidden="true"
></div>

{{-- Mobile bottom sheet: always mounted, slides up via transform like the side menu --}}
<div
    x-cloak
    x-bind:class="{ 'translate-y-full': ! isCommandOpen }"
    class="md:hidden fixed bottom-0 inset-x-0 w-screen z-300 flex h-[75vh] flex-col overflow-hidden border-t border-(--lh-border) bg-(--lh-card) shadow-[0_-12px_40px_rgba(0,0,0,0.3)] transition-transform duration-250 ease-in-out"
    role="dialog"
    aria-modal="true"
    aria-label="Command window"
>
    {{-- Input row --}}
    <div class="flex h-13 shrink-0 items-center gap-2.5 border-b border-(--lh-border) px-4">
        <span class="select-none font-mono text-[15px] font-bold text-(--lh-accent)" aria-hidden="true">❯</span>
        <input
            x-ref="commandInputMobile"
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
