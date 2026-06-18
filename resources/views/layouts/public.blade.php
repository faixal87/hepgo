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
<body class="min-h-screen bg-orange-50 font-sans text-zinc-900 antialiased">
    <div class="min-h-screen">
        <header class="sticky top-0 z-40 border-b border-white/70 bg-white/90 shadow-sm backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo_polimas.png') }}" alt="Logo POLIMAS" class="h-12 w-auto object-contain">
                    <span class="leading-tight">
                        <span class="block text-sm font-extrabold text-zinc-950 sm:text-base">Portal Rumah Sewa HEP</span>
                        <span class="block text-xs font-medium text-orange-700">Rujukan luar kampus POLIMAS</span>
                    </span>
                </a>

                <nav class="flex items-center gap-3 text-sm font-semibold">
                    <div class="hidden items-center gap-3 rounded-full bg-white px-3 py-1.5 text-xs font-bold text-zinc-500 ring-1 ring-zinc-200 md:inline-flex">
                        <span class="inline-flex items-center gap-1.5">
                            <x-lucide-users class="h-4 w-4 text-blue-700" />
                            Hari ini {{ number_format($visitorStats['today_unique'] ?? 0) }}
                        </span>
                        <span class="h-4 w-px bg-zinc-200"></span>
                        <span class="inline-flex items-center gap-1.5">
                            <x-lucide-eye class="h-4 w-4 text-orange-600" />
                            Jumlah {{ number_format($visitorStats['total_unique'] ?? 0) }}
                        </span>
                    </div>
                    <div class="inline-flex items-center gap-1.5 rounded-full bg-white px-2.5 py-1.5 text-[11px] font-bold text-zinc-500 ring-1 ring-zinc-200 md:hidden">
                        <x-lucide-users class="h-3.5 w-3.5 text-blue-700" />
                        {{ number_format($visitorStats['today_unique'] ?? 0) }}
                    </div>
                    <a href="{{ route('properties.index') }}" class="rounded-full px-3 py-2 text-zinc-700 transition hover:bg-orange-50 hover:text-orange-700">
                        Rumah Sewa
                    </a>
                    @auth
                        <a href="/admin" class="rounded-full bg-blue-700 px-4 py-2 text-white shadow-sm transition hover:bg-blue-800">
                            Admin Panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full bg-blue-700 px-4 py-2 text-white shadow-sm transition hover:bg-blue-800">
                            Login
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer class="border-t border-zinc-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-zinc-500 sm:px-6 lg:px-8">
                <div class="inline-flex flex-wrap items-center justify-center gap-3">
                    <img src="{{ asset('images/logo_jtmk.png') }}" alt="Logo JTMK" class="h-8 w-auto object-contain">
                    <p>&copy; {{ date('Y') }} Portal Rumah Sewa HEP. Semua hak cipta terpelihara JTMK POLIMAS</p>
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
