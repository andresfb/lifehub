<x-layouts.auth description="{{ __('Sign in to your account') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium" style="color:var(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px]" style="color:#e54">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-[13px] font-medium mb-1.5" style="color:var(--lh-text-sec)">
                {{ __('Email') }}
            </label>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="you@example.com"
                class="lh-input w-full h-11 rounded-[10px] px-3.5 text-[14px] border"
                style="background:var(--lh-input);color:var(--lh-text);border-color:var(--lh-border);transition:border-color 0.2s,box-shadow 0.2s;font-family:inherit"
                onfocus="this.style.borderColor='var(--lh-accent)';this.style.boxShadow='0 0 0 3px oklch(0.65 0.15 175 / 0.12)'"
                onblur="this.style.borderColor='var(--lh-border)';this.style.boxShadow='none'"
            />
            @error('email')
                <p class="mt-1 text-[12px]" style="color:#e54">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-[13px] font-medium mb-1.5" style="color:var(--lh-text-sec)">
                {{ __('Password') }}
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full h-11 rounded-[10px] px-3.5 text-[14px] border"
                style="background:var(--lh-input);color:var(--lh-text);border-color:var(--lh-border);transition:border-color 0.2s,box-shadow 0.2s;font-family:inherit"
                onfocus="this.style.borderColor='var(--lh-accent)';this.style.boxShadow='0 0 0 3px oklch(0.65 0.15 175 / 0.12)'"
                onblur="this.style.borderColor='var(--lh-border)';this.style.boxShadow='none'"
            />
            @error('password')
                <p class="mt-1 text-[12px]" style="color:#e54">{{ $message }}</p>
            @enderror
        </div>

        {{-- Forgot password --}}
        <div class="flex justify-end">
            <button type="button" class="border-none bg-transparent text-[13px] font-medium cursor-pointer" style="color:var(--lh-accent-text)">
                {{ __('Forgot password?') }}
            </button>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full h-11 rounded-[10px] border-none text-white text-[15px] font-semibold cursor-pointer"
            style="background:var(--lh-accent);font-family:inherit;transition:opacity 0.15s,transform 0.1s"
            onmouseenter="this.style.opacity='0.9'"
            onmouseleave="this.style.opacity='1'"
            onmousedown="this.style.transform='scale(0.98)'"
            onmouseup="this.style.transform='scale(1)'"
        >
            {{ __('Sign In') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold no-underline" style="color:var(--lh-accent-text)">
            {{ __('Create one') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
