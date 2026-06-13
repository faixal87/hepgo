<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="@yield('meta_description', 'Portal Rumah Sewa HEP membantu pelajar dan ibu bapa mencari maklumat rumah sewa luar kampus yang disahkan.')">

    <title>@yield('title', 'Portal Rumah Sewa HEP')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#f5f7f2] font-sans text-zinc-900 antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-white/70 bg-white/90 shadow-sm backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="grid h-10 w-10 place-items-center rounded-2xl bg-emerald-700 text-sm font-extrabold text-white shadow-sm">HEP</span>
                    <span class="leading-tight">
                        <span class="block text-sm font-extrabold text-zinc-950 sm:text-base">Portal Rumah Sewa</span>
                        <span class="block text-xs font-medium text-zinc-500">Rujukan luar kampus</span>
                    </span>
                </a>

                <nav class="flex items-center gap-2 text-sm font-semibold">
                    <a href="{{ route('properties.index') }}" class="rounded-full px-3 py-2 text-zinc-700 transition hover:bg-emerald-50 hover:text-emerald-800">
                        Rumah Sewa
                    </a>
                    @auth
                        <a href="/admin" class="rounded-full bg-zinc-950 px-4 py-2 text-white shadow-sm transition hover:bg-zinc-800">
                            Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-zinc-950 px-4 py-2 text-white shadow-sm transition hover:bg-zinc-800">
                            Log Masuk
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-zinc-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-8 text-sm text-zinc-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                <p>&copy; {{ date('Y') }} Portal Rumah Sewa HEP. Semua hak cipta terpelihara.</p>
                <p>Maklumat rumah sewa disediakan sebagai rujukan pelajar dan ibu bapa.</p>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
