<x-layouts.auth>
    <div class="space-y-6">
        <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form method="post" :action="route('login.store')" class="space-y-6">
            @csrf

            <x-input
                type="email"
                :label="__('Email address')"
                name="email"
                required
                autofocus
                autocomplete="email"
            />

            <div class="relative">
                <x-input
                    type="password"
                    :label="__('Password')"
                    name="password"
                    required
                    autocomplete="current-password"
                />

            </div>

            <button class="w-full">{{ __('Log in') }}</button>
        </x-form>
    </div>
</x-layouts.auth>
