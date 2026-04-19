<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-screen bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
    <main class="flex-1 flex flex-col">
        <x-container class="flex-1 flex flex-col py-6 lg:py-8">
            {{ $slot }}
        </x-container>
    </main>
</body>
</html>
