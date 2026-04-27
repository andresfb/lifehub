@props(['inputName'])

<div>
    {{-- Input row --}}
    <div class="flex h-13 shrink-0 items-center gap-2.5 border-b border-(--lh-border) px-4">
        <span class="select-none font-mono text-[15px] font-bold text-(--lh-accent)" aria-hidden="true">❯</span>
        <input
            x-ref="{{ $inputName }}"
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

    <div class="min-h-30 flex-1 overflow-y-auto px-4 py-3 font-mono text-[13px] leading-[1.7]">
        <ul class="mt-1 text-(--lh-text-sec)">
            <li>?&nbsp;&nbsp;Module Search</li>
            <li>??&nbsp;Global Search</li>
            <li>/&nbsp;&nbsp;Go to Module</li>
            <li>>&nbsp;&nbsp;Commands</li>
            <li>+&nbsp;&nbsp;Actions</li>
        </ul>
    </div>
</div>
