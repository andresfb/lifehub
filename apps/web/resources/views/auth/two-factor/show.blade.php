<x-layouts.auth description="{{ __('Enter the authentication code sent to your device') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium text-(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px] text-[#e54]">{{ session('error') }}</p>
    @endif

    {{-- Countdown --}}
    @if(session('tfa-ttl'))
        <div class="mb-5 flex flex-col items-center gap-1">
            <div id="countdown-ring" class="relative w-24 h-24">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 80 80">
                    <circle cx="40" cy="40" r="34" fill="none" stroke="var(--lh-border)" stroke-width="4"/>
                    <circle
                        id="countdown-arc"
                        cx="40" cy="40" r="34"
                        fill="none"
                        stroke="var(--lh-accent)"
                        stroke-width="4"
                        stroke-linecap="round"
                        stroke-dasharray="213.6"
                        stroke-dashoffset="0"
                    />
                </svg>
                <span id="countdown-text"
                    class="absolute inset-0 flex items-center justify-center font-display text-[18px] font-bold text-(--lh-text) tabular-nums"
                ></span>
            </div>
            <p id="countdown-label" class="text-[12px] text-(--lh-text-muted)">{{ __('remaining') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login.two-factor.store') }}">
        @csrf

        <div class="mb-5">
            <label for="code" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                {{ __('Authentication Code') }}
            </label>
            <input
                id="code"
                type="text"
                name="code"
                required
                autofocus
                autocomplete="one-time-code"
                inputmode="numeric"
                maxlength="6"
                placeholder="000000"
                class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-center font-display text-[14px] font-bold tracking-[0.4em] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
            />
            @error('code')
                <p class="mt-1 text-[12px] text-[#e54]">{{ $message }}</p>
            @enderror
        </div>

        <button
            id="submit-btn"
            type="submit"
            class="h-11 w-full cursor-pointer rounded-[10px] border-none bg-(--lh-accent) text-[15px] font-semibold text-white transition-[opacity,transform] duration-150 hover:opacity-90 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-40 disabled:hover:opacity-40"
        >
            {{ __('Verify') }}
        </button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="font-medium text-(--lh-text-muted) no-underline">
            ← {{ __('Back to login') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>

@if(session('tfa-ttl'))
<script>
(function () {
    const total     = {{ (int) session('tfa-ttl') }};
    const arc       = document.getElementById('countdown-arc');
    const text      = document.getElementById('countdown-text');
    const subLabel  = document.getElementById('countdown-label');
    const btn       = document.getElementById('submit-btn');
    const circum    = 213.6; // 2π × 34
    let   remaining = total;

    function format(s) {
        if (s < 60) return String(s);
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return m + ':' + String(sec).padStart(2, '0');
    }

    function tick() {
        text.textContent = format(remaining);
        subLabel.textContent = remaining < 60 ? 'seconds remaining' : 'remaining';
        arc.style.strokeDashoffset = circum * (1 - remaining / total);

        if (remaining <= 0) {
            btn.disabled = true;
            text.textContent  = '0';
            text.classList.remove('text-(color:--lh-text)');
            text.classList.add('text-[#e54]');
            arc.setAttribute('stroke', '#e54');
            return;
        }

        remaining--;
        setTimeout(tick, 1000);
    }

    tick();
})();
</script>
@endif
