<x-layouts.auth description="{{ __('Create your account') }}">

    @if(session('message'))
        <div class="alert alert-success alert-soft text-sm">{{ session('message') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error alert-soft text-sm">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        @foreach([
            ['name',             'text',     __('Full Name'),         'name',             'John Doe',           true],
            ['email',            'email',    __('Email'),             'email',            'you@example.com',    true],
            ['password',         'password', __('Password'),          'new-password',     '••••••••',           false],
            ['password_confirmation', 'password', __('Confirm Password'), 'new-password', '••••••••',           false],
            ['invitation',  'text',     __('Invitation Code'),   'off',              __('Enter your invite code'), false],
        ] as [$fieldName, $fieldType, $fieldLabel, $fieldAuto, $fieldPlaceholder, $fieldAutofocus])

            <div>
                <label for="{{ $fieldName }}" class="label px-0">
                    <span class="label-text font-medium">{{ $fieldLabel }}</span>
                </label>
                <input
                    id="{{ $fieldName }}"
                    type="{{ $fieldType }}"
                    name="{{ $fieldName }}"
                    value="{{ $fieldType !== 'password' ? old($fieldName) : '' }}"
                    required
                    autocomplete="{{ $fieldAuto }}"
                    placeholder="{{ $fieldPlaceholder }}"
                    {{ $fieldAutofocus ? 'autofocus' : '' }}
                    class="input input-bordered w-full"
                />
                @error($fieldName)
                    <p class="mt-1 text-xs text-error">{{ $message }}</p>
                @enderror
            </div>

        @endforeach

        <div class="h-1"></div>

        <button
            type="submit"
            class="btn btn-primary w-full"
        >
            {{ __('Create Account') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="link link-hover link-primary font-semibold no-underline">
            {{ __('Sign in') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
