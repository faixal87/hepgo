<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white text-gray-900 antialiased">
        <header class="border-b border-gray-200">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-md bg-sky-700 text-sm font-bold text-white">HEP</span>
                    <span class="font-semibold">Portal Rumah Sewa HEP</span>
                </a>

                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        @if (Auth::user()->hasAnyRole(config('hep.admin_panel_roles')))
                            <a href="/admin" class="rounded-md px-3 py-2 font-medium text-gray-700 hover:bg-gray-100">Admin</a>
                        @endif

                        <a href="{{ route('dashboard') }}" class="rounded-md px-3 py-2 font-medium text-gray-700 hover:bg-gray-100">Papan Pemuka</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md px-3 py-2 font-medium text-gray-700 hover:bg-gray-100">Log Masuk</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="rounded-md bg-sky-700 px-4 py-2 font-medium text-white hover:bg-sky-800">Daftar</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl gap-10 px-6 py-16 lg:grid-cols-[1fr_420px] lg:items-center">
                <div>
                    <p class="text-sm font-semibold uppercase text-sky-700">Penginapan luar kampus disahkan</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-semibold leading-tight text-gray-950 md:text-5xl">
                        Portal Rumah Sewa HEP
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg leading-8 text-gray-600">
                        Platform asas untuk membantu HEP mengurus senarai rumah sewa luar kampus yang telah disemak, supaya pelajar baharu dan ibu bapa boleh mendapatkan maklumat penginapan dengan lebih teratur.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @guest
                            <a href="{{ route('login') }}" class="rounded-md bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800">Log Masuk</a>
                            <a href="{{ route('register') }}" class="rounded-md border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-800 hover:bg-gray-50">Daftar Akaun</a>
                        @else
                            <a href="{{ route('dashboard') }}" class="rounded-md bg-sky-700 px-5 py-3 text-sm font-semibold text-white hover:bg-sky-800">Buka Papan Pemuka</a>
                        @endguest
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-5">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                        <div>
                            <div class="text-sm font-semibold text-gray-950">Status Sistem</div>
                            <div class="text-sm text-gray-500">Sprint 1</div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                    </div>

                    <dl class="mt-5 space-y-4 text-sm">
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">Pengesahan pengguna</dt>
                            <dd class="font-medium text-gray-950">Breeze</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">Panel admin</dt>
                            <dd class="font-medium text-gray-950">/admin</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">API versi</dt>
                            <dd class="font-medium text-gray-950">/api/v1</dd>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <dt class="text-gray-500">Bahasa antara muka</dt>
                            <dd class="font-medium text-gray-950">Bahasa Melayu</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <section class="border-t border-gray-200 bg-gray-50">
                <div class="mx-auto grid max-w-7xl gap-6 px-6 py-10 md:grid-cols-3">
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="font-semibold text-gray-950">Senarai Disahkan</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Asas sistem disediakan untuk semakan dan penerbitan rumah sewa yang dipercayai.</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="font-semibold text-gray-950">Akses Berperanan</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Pentadbir, staf HEP, pemilik rumah, pelajar dan ibu bapa diasingkan melalui peranan dan kebenaran.</p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5">
                        <h2 class="font-semibold text-gray-950">Sedia API</h2>
                        <p class="mt-2 text-sm leading-6 text-gray-600">Struktur `/api/v1` dan Sanctum disediakan untuk integrasi aplikasi Android pada sprint akan datang.</p>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
