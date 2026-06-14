<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    @php
        $theme = auth()->user()?->uiTheme() ?? config('hep.ui_themes.polimas_biru_oren');
    @endphp
    <body class="font-sans antialiased">
        <div
            class="hep-shell"
            style="--hep-sidebar: {{ $theme['sidebar'] }}; --hep-sidebar-secondary: {{ $theme['sidebar_secondary'] }}; --hep-accent: {{ $theme['accent'] }}; --hep-accent-soft: {{ $theme['accent_soft'] }}; --hep-workspace: {{ $theme['workspace'] }};"
        >
            <div class="flex min-h-screen">
                <aside class="hep-sidebar hidden w-72 shrink-0 flex-col px-5 py-6 lg:flex">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <span class="rounded-2xl bg-white p-2 shadow-sm">
                            <x-application-logo class="h-12 w-auto object-contain" />
                        </span>
                        <span class="leading-tight">
                            <span class="block text-sm font-black">Portal Rumah Sewa HEP</span>
                            <span class="block text-xs font-bold text-white/70">Workspace Pengguna</span>
                        </span>
                    </a>

                    <nav class="mt-8 space-y-2">
                        <a href="{{ route('dashboard') }}" class="hep-sidebar-link {{ request()->routeIs('dashboard') ? 'hep-sidebar-link-active' : '' }}">
                            <x-lucide-layout-dashboard class="h-5 w-5" />
                            <span>Papan Pemuka</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" class="hep-sidebar-link {{ request()->routeIs('profile.edit') ? 'hep-sidebar-link-active' : '' }}">
                            <x-lucide-user-circle class="h-5 w-5" />
                            <span>Profil & Tema</span>
                        </a>
                        @if (auth()->user()?->hasAnyRole(config('hep.admin_panel_roles')))
                            <a href="/admin" class="hep-sidebar-link">
                                <x-lucide-shield-check class="h-5 w-5" />
                                <span>Panel Admin</span>
                            </a>
                        @endif
                    </nav>

                    <div class="mt-auto rounded-2xl bg-white/10 p-4 text-xs font-semibold leading-5 text-white/78">
                        Tema aktif: {{ auth()->user()?->uiTheme()['label'] ?? 'Biru POLIMAS + Oren' }}
                    </div>
                </aside>

                <div class="min-w-0 flex-1">
                    @include('layouts.navigation')

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="border-b border-slate-200 bg-white/80 shadow-sm backdrop-blur">
                            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main>
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
