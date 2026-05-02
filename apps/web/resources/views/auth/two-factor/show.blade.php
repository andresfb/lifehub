<x-layouts.auth description="{{ __('Enter the authentication code sent to your device') }}">

    @if(session('message'))
        <div class="alert alert-success alert-soft text-sm">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error alert-soft text-sm">{{ session('error') }}</div>
    @endif

    <div @if(session('tfa-ttl')) x-data="twoFactorCountdown({{ (int) session('tfa-ttl') }})" @endif>
        {{-- Countdown --}}
        @if(session('tfa-ttl'))
            <div class="mb-5 flex flex-col items-center gap-1">
                <div class="relative w-24 h-24">
                    <svg class="w-full h-full -rotate-90" viewBox="0 0 80 80">
                        <circle cx="40" cy="40" r="34" fill="none" stroke="currentColor" stroke-width="4" class="text-base-300"/>
                        <circle
                            cx="40"
                            cy="40"
                            r="34"
                            fill="none"
                            x-bind:stroke="hasExpired ? '#ef4444' : 'var(--color-primary)'"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-dasharray="213.6"
                            x-bind:stroke-dashoffset="arcOffset"
                        />
                    </svg>
                    <span
                        x-text="text"
                        x-bind:class="{ 'text-error': hasExpired, 'text-base-content': ! hasExpired }"
                        class="absolute inset-0 flex items-center justify-center font-display text-[18px] font-bold tabular-nums"
                    >{{ (int) session('tfa-ttl') < 60 ? (int) session('tfa-ttl') : floor((int) session('tfa-ttl') / 60).':'.str_pad((string) ((int) session('tfa-ttl') % 60), 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <p x-text="label" class="text-xs text-base-content/60">{{ __('remaining') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login.two-factor.store') }}">
            @csrf

            <div class="mb-5">
                <label for="code" class="label px-0">
                    <span class="label-text font-medium">{{ __('Authentication Code') }}</span>
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
                    class="input input-bordered w-full text-center font-display font-bold tracking-[0.4em]"
                />
                @error('code')
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                @if(session('tfa-ttl')) x-bind:disabled="hasExpired" @endif
                class="btn btn-primary w-full disabled:btn-disabled"
            >
                {{ __('Verify') }}
            </button>
        </form>
    </div>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="link link-hover text-base-content/70 no-underline">
            ← {{ __('Back to login') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
