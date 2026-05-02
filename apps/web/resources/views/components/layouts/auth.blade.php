@props(['title' => '', 'description' => ''])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="bg-base-200">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'LifeHub') }}{{ $title ? ' — '.$title : '' }}</title>
    <script>
        document.documentElement.dataset.theme = localStorage.getItem('lh_theme') === 'dark' ? 'forest' : 'emerald';
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200 p-5">

    <div class="mx-auto flex min-h-screen w-full max-w-md items-center justify-center">
        <div class="w-full">
        {{-- Logo + app name --}}
        <div class="mb-8 text-center">
            <a href="{{ route('login') }}" class="mb-2 inline-flex items-center gap-3 no-underline">
                <x-logo :size="36" />
                <span class="font-display text-[26px] font-extrabold tracking-[-0.5px] text-base-content">LifeHub</span>
            </a>
            @if($description)
                <p class="text-sm text-base-content/70">{{ $description }}</p>
            @endif
        </div>

        {{-- Card --}}
        <div class="card border border-base-300 bg-base-100 shadow-xl">
            <div class="card-body gap-6 p-7">
                {{ $slot }}
            </div>
        </div>

        {{-- Footer slot (links below card) --}}
        @isset($footer)
            <div class="mt-5 text-center text-sm text-base-content/70">
                {{ $footer }}
            </div>
        @endisset
        </div>
    </div>

</body>
</html>
