<x-layouts.auth description="{{ __('Create your account') }}">

    @if(session('message'))
        <p class="mb-4 text-center text-[13px] font-medium" style="color:var(--lh-accent-text)">
            {{ session('message') }}
        </p>
    @endif

    @if(session('error'))
        <p class="mb-4 text-center text-[13px]" style="color:#e54">{{ session('error') }}</p>
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
                <label for="{{ $fieldName }}" class="block text-[13px] font-medium mb-1.5" style="color:var(--lh-text-sec)">
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
                    class="w-full h-11 rounded-[10px] px-3.5 text-[14px] border"
                    style="background:var(--lh-input);color:var(--lh-text);border-color:var(--lh-border);transition:border-color 0.2s,box-shadow 0.2s;font-family:inherit"
                    onfocus="this.style.borderColor='var(--lh-accent)';this.style.boxShadow='0 0 0 3px oklch(0.65 0.15 175 / 0.12)'"
                    onblur="this.style.borderColor='var(--lh-border)';this.style.boxShadow='none'"
                />
                @error($fieldName)
                    <p class="mt-1 text-[12px]" style="color:#e54">{{ $message }}</p>
                @enderror
            </div>

        @endforeach

        <div class="h-1"></div>

        <button
            type="submit"
            class="w-full h-11 rounded-[10px] border-none text-white text-[15px] font-semibold cursor-pointer"
            style="background:var(--lh-accent);font-family:inherit;transition:opacity 0.15s,transform 0.1s"
            onmouseenter="this.style.opacity='0.9'"
            onmouseleave="this.style.opacity='1'"
            onmousedown="this.style.transform='scale(0.98)'"
            onmouseup="this.style.transform='scale(1)'"
        >
            {{ __('Create Account') }}
        </button>
    </form>

    <x-slot:footer>
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold no-underline" style="color:var(--lh-accent-text)">
            {{ __('Sign in') }}
        </a>
    </x-slot:footer>

</x-layouts.auth>
