<x-layouts.auth>
    <div class="space-y-6">
        <x-auth-header
            :title="__('Authentication Code')"
            :description="__('Enter the authentication code provided by your authenticator application.')"
        />

        <x-form method="post" action="{{ route('login.two-factor.store') }}">
            <div class="space-y-5 text-center">
                <x-field>
                    <x-label for="code" class="sr-only" :value="__('OTP Code')" />
                    <x-error for="code" />
                    <x-input inputmode="numeric" name="code" autofocus autocomplete="one-time-code" class="mx-auto max-w-[16ch]" placeholder="• • • • • •" />
                </x-field>

                <x-button variant="primary" class="mt-5 w-full">{{ __('Continue') }}</x-button>
            </div>
        </x-form>
    </div>
</x-layouts.auth>
