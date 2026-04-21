<x-layouts.auth description="{{ __('Sign in to your account') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium text-(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px] text-[#e54]">{{ session('error') }}</p>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
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
                class="lh-input h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
            />
            @error('email')
                <p class="mt-1 text-[12px] text-[#e54]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                {{ __('Password') }}
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
            />
            @error('password')
                <p class="mt-1 text-[12px] text-[#e54]">{{ $message }}</p>
            @enderror
        </div>

        {{-- Forgot password --}}
        <div class="flex justify-end">
            <button type="button" class="cursor-pointer border-none bg-transparent text-[13px] font-medium text-(--lh-accent-text)">
                {{ __('Forgot password?') }}
            </button>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="h-11 w-full cursor-pointer rounded-[10px] border-none bg-(--lh-accent) text-[15px] font-semibold text-white transition-[opacity,transform] duration-150 hover:opacity-90 active:scale-[0.98]"
        >
            {{ __('Sign In') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="font-semibold text-(--lh-accent-text) no-underline">
            {{ __('Create one') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
