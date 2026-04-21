@props(['title' => '', 'description' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }}{{ $title ? ' — '.$title : '' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center bg-(--lh-bg) p-5">

    <div class="w-full max-w-100">
        {{-- Logo + app name --}}
        <div class="text-center mb-9">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2.5 no-underline mb-2">
                <x-logo :size="36" />
                <span class="font-display font-extrabold text-[26px] tracking-[-0.5px] text-(--lh-text)">LifeHub</span>
            </a>
            @if($description)
                <p class="text-[14px] text-(--lh-text-muted)">{{ $description }}</p>
            @endif
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-(--lh-border) bg-(--lh-card) px-7 py-8 shadow-(--lh-shadow-lg)">
            {{ $slot }}
        </div>

        {{-- Footer slot (links below card) --}}
        @isset($footer)
            <div class="mt-5 text-center text-[14px] text-(--lh-text-muted)">
                {{ $footer }}
            </div>
        @endisset
    </div>

</body>
</html>
