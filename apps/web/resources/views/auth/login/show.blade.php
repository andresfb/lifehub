<x-layouts.auth description="{{ __('Sign in to your account') }}">

    @if(session('message'))
        <div class="alert alert-success alert-soft text-sm">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error alert-soft text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="label px-0">
                <span class="label-text font-medium">{{ __('Email') }}</span>
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
                class="input input-bordered w-full"
            />
            @error('email')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="label px-0">
                <span class="label-text font-medium">{{ __('Password') }}</span>
            </label>
            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="input input-bordered w-full"
            />
            @error('password')
                <p class="mt-1 text-xs text-error">{{ $message }}</p>
            @enderror
        </div>

        {{-- Forgot password --}}
        <div class="flex justify-end">
            <button type="button" class="link link-hover link-primary text-sm font-medium">
                {{ __('Forgot password?') }}
            </button>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="btn btn-primary w-full"
        >
            {{ __('Sign In') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="link link-hover link-primary font-semibold no-underline">
            {{ __('Create one') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
