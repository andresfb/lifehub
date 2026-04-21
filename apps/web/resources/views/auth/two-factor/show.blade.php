<x-layouts.auth description="{{ __('Enter the authentication code sent to your device') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium" style="color:var(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px]" style="color:#e54">{{ session('error') }}</p>
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
                    class="absolute inset-0 flex items-center justify-center font-display font-bold text-[18px] tabular-nums"
                    style="color:var(--lh-text)"
                ></span>
            </div>
            <p id="countdown-label" class="text-[12px]" style="color:var(--lh-text-muted)">{{ __('remaining') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login.two-factor.store') }}">
        @csrf

        <div class="mb-5">
            <label for="code" class="block text-[13px] font-medium mb-1.5" style="color:var(--lh-text-sec)">
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
                class="w-full h-11 rounded-[10px] px-3.5 text-[14px] border text-center tracking-[0.5em] font-display font-bold"
                style="background:var(--lh-input);color:var(--lh-text);border-color:var(--lh-border);transition:border-color 0.2s,box-shadow 0.2s;font-family:inherit;letter-spacing:0.4em"
                onfocus="this.style.borderColor='var(--lh-accent)';this.style.boxShadow='0 0 0 3px oklch(0.65 0.15 175 / 0.12)'"
                onblur="this.style.borderColor='var(--lh-border)';this.style.boxShadow='none'"
            />
            @error('code')
                <p class="mt-1 text-[12px]" style="color:#e54">{{ $message }}</p>
            @enderror
        </div>

        <button
            id="submit-btn"
            type="submit"
            class="w-full h-11 rounded-[10px] border-none text-white text-[15px] font-semibold cursor-pointer"
            style="background:var(--lh-accent);font-family:inherit;transition:opacity 0.15s,transform 0.1s"
            onmouseenter="this.style.opacity='0.9'"
            onmouseleave="this.style.opacity='1'"
            onmousedown="this.style.transform='scale(0.98)'"
            onmouseup="this.style.transform='scale(1)'"
        >
            {{ __('Verify') }}
        </button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="font-medium no-underline" style="color:var(--lh-text-muted)">
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
            btn.style.opacity = '0.4';
            btn.style.cursor  = 'not-allowed';
            text.textContent  = '0';
            text.style.color  = '#e54';
            arc.style.stroke  = '#e54';
            return;
        }

        remaining--;
        setTimeout(tick, 1000);
    }

    tick();
})();
</script>
@endif
