<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Portal Rumah Sewa HEP') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center bg-gradient-to-br from-slate-50 via-white to-orange-50 px-4 pt-6 sm:justify-center sm:pt-0">
            <div class="flex flex-col items-center">
                <a href="/" class="flex justify-center">
                    <x-application-logo class="h-20 w-auto object-contain sm:h-24" />
                </a>
            </div>

            <div class="mt-6 w-full overflow-hidden rounded-3xl bg-white px-6 py-6 shadow-xl shadow-slate-200/70 ring-1 ring-slate-200 sm:max-w-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
