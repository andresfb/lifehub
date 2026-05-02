@props(['inputName'])

<div>
    {{-- Input row --}}
    <div class="flex h-14 shrink-0 items-center gap-3 border-b border-base-300 px-4">
        <span class="select-none font-mono text-base font-bold text-primary" aria-hidden="true">❯</span>
        <input
            x-ref="{{ $inputName }}"
            type="text"
            placeholder="Type a command..."
            class="input input-ghost h-full flex-1 border-0 bg-transparent px-0 text-sm shadow-none focus:outline-none"
            autocomplete="off"
            spellcheck="false"
        />
        <kbd class="kbd kbd-sm" x-text="/Mac|iPhone|iPad/.test(navigator.platform) ? '⌘/' : 'Ctrl+/'"></kbd>
    </div>

    <div class="min-h-30 flex-1 overflow-y-auto px-4 py-4 font-mono text-sm leading-7">
        <ul class="menu w-full gap-1 rounded-box bg-base-200/60 p-2 text-base-content/70">
            <li>?&nbsp;&nbsp;Module Search</li>
            <li>??&nbsp;Global Search</li>
            <li>/&nbsp;&nbsp;Go to Module</li>
            <li>>&nbsp;&nbsp;Commands</li>
            <li>+&nbsp;&nbsp;Actions</li>
        </ul>
    </div>
</div>
