<x-layouts.auth description="{{ __('Create your account') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium text-(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px] text-[#e54]">{{ session('error') }}</p>
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
                <label for="{{ $fieldName }}" class="mb-1.5 block text-[13px] font-medium text-(--lh-text-sec)">
                    {{ $fieldLabel }}
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
                    class="h-11 w-full rounded-[10px] border border-(--lh-border) bg-(--lh-input) px-3.5 text-[14px] text-(--lh-text) transition-[border-color,box-shadow] duration-200 focus:border-(--lh-accent) focus:shadow-[0_0_0_3px_oklch(0.65_0.15_175/0.12)]"
                />
                @error($fieldName)
                    <p class="mt-1 text-[12px] text-[#e54]">{{ $message }}</p>
                @enderror
            </div>

        @endforeach

        <div class="h-1"></div>

        <button
            type="submit"
            class="h-11 w-full cursor-pointer rounded-[10px] border-none bg-(--lh-accent) text-[15px] font-semibold text-white transition-[opacity,transform] duration-150 hover:opacity-90 active:scale-[0.98]"
        >
            {{ __('Create Account') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold text-(--lh-accent-text) no-underline">
            {{ __('Sign in') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
