@extends('layouts.public')

@section('title', 'Portal Rumah Sewa HEP')
@section('meta_description', 'Cari rumah sewa luar kampus yang disahkan oleh HEP untuk rujukan pelajar baharu dan ibu bapa.')

@section('content')
    <section class="relative overflow-hidden bg-white">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-8 sm:px-6 md:grid-cols-[1.1fr_0.9fr] md:items-center md:py-12 lg:px-8">
            <div class="space-y-6">
                <div class="inline-flex rounded-full bg-orange-50 px-4 py-2 text-xs font-extrabold uppercase tracking-wide text-orange-700 ring-1 ring-orange-100">
                    Rujukan rasmi HEP POLIMAS
                </div>

                <div class="space-y-4">
                    <h1 class="max-w-3xl text-4xl font-extrabold leading-tight text-zinc-950 sm:text-5xl">
                        Cari Rumah Sewa Luar Kampus Dengan Mudah
                    </h1>
                    <p class="max-w-2xl text-base leading-7 text-zinc-600 sm:text-lg">
                        Portal ini membantu pelajar baharu dan ibu bapa mendapatkan maklumat rumah sewa sekitar kampus yang diurus dan dikemaskini oleh HEP.
                    </p>
                </div>

                <div class="rounded-2xl bg-orange-50 p-4 text-sm font-semibold leading-6 text-orange-900 ring-1 ring-orange-100">
                    HEP menyediakan maklumat sebagai rujukan. Urusan sewaan adalah antara pelajar/ibu bapa dan pemilik rumah.
                </div>

                <form action="{{ route('properties.index') }}" method="GET" class="grid gap-3 rounded-2xl bg-zinc-50 p-3 shadow-sm ring-1 ring-zinc-200 sm:grid-cols-[1fr_auto]">
                    <label for="keyword" class="sr-only">Cari rumah sewa</label>
                    <input
                        id="keyword"
                        name="keyword"
                        type="search"
                        placeholder="Cari kawasan, alamat atau nama rumah"
                        class="min-h-12 rounded-xl border-zinc-200 bg-white text-sm font-semibold shadow-sm focus:border-orange-500 focus:ring-orange-500"
                    >
                    <button type="submit" class="min-h-12 rounded-xl bg-orange-600 px-6 text-sm font-extrabold text-white shadow-sm transition hover:bg-orange-700">
                        Cari Rumah
                    </button>
                </form>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('properties.index', ['status' => 'available']) }}" class="rounded-full bg-orange-100 px-4 py-2 text-sm font-bold text-orange-800 ring-1 ring-orange-200">
                        Masih Kosong
                    </a>
                    <a href="{{ route('properties.index', ['sort' => 'distance']) }}" class="rounded-full bg-blue-100 px-4 py-2 text-sm font-bold text-blue-800 ring-1 ring-blue-200">
                        Jarak Terdekat
                    </a>
                    <a href="{{ route('properties.index', ['gender' => 'family']) }}" class="rounded-full bg-orange-100 px-4 py-2 text-sm font-bold text-orange-800 ring-1 ring-orange-200">
                        Sesuai Keluarga
                    </a>
                    @foreach ($categories->take(2) as $category)
                        <a href="{{ route('properties.index', ['category' => $category->id]) }}" class="rounded-full bg-zinc-100 px-4 py-2 text-sm font-bold text-zinc-700 ring-1 ring-zinc-200">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-4">
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-orange-600 p-4 text-white shadow-sm">
                        <p class="text-3xl font-extrabold">{{ number_format($verifiedCount) }}</p>
                        <p class="mt-1 text-xs font-bold leading-5 text-orange-50">Jumlah Rumah Disahkan</p>
                    </div>
                    <div class="rounded-2xl bg-blue-800 p-4 text-white shadow-sm">
                        <p class="text-3xl font-extrabold">{{ number_format($availableCount) }}</p>
                        <p class="mt-1 text-xs font-bold leading-5 text-blue-100">Rumah Masih Kosong</p>
                    </div>
                    <div class="rounded-2xl bg-white p-4 text-blue-900 shadow-sm ring-1 ring-orange-100">
                        <p class="text-3xl font-extrabold">{{ number_format($areaCount) }}</p>
                        <p class="mt-1 text-xs font-bold leading-5">Kawasan Diliputi</p>
                    </div>
                </div>

                <div class="overflow-hidden rounded-3xl bg-zinc-950 shadow-sm">
                    @if ($latestProperties->first()?->thumbnailUrl())
                        <img src="{{ $latestProperties->first()->thumbnailUrl() }}" alt="Gambar rumah sewa terkini" class="h-64 w-full object-cover opacity-90">
                    @else
                    <div class="flex h-64 flex-col justify-end bg-gradient-to-br from-orange-600 via-blue-900 to-white p-6">
                        <p class="max-w-xs text-2xl font-extrabold leading-tight text-white">Maklumat rumah sewa yang mudah disemak dari telefon.</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-px bg-white/10 p-1">
                        @foreach ($areas->take(4) as $area)
                            <a href="{{ route('properties.index', ['area' => $area->id]) }}" class="rounded-2xl bg-white/10 px-4 py-3 text-sm font-bold text-white backdrop-blur transition hover:bg-white/20">
                                {{ $area->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-5 flex items-end justify-between gap-4">
            <div>
                <p class="text-sm font-extrabold uppercase tracking-wide text-orange-700">Pilihan terkini</p>
                <h2 class="mt-1 text-2xl font-extrabold text-zinc-950">Rumah Masih Kosong</h2>
            </div>
            <a href="{{ route('properties.index') }}" class="rounded-full bg-white px-4 py-2 text-sm font-bold text-zinc-700 shadow-sm ring-1 ring-zinc-200 transition hover:bg-zinc-50">
                Lihat Semua
            </a>
        </div>

        @if ($latestProperties->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($latestProperties as $property)
                    @include('public.properties._card', ['property' => $property])
                @endforeach
            </div>
        @else
            <div class="rounded-2xl bg-white p-8 text-center shadow-sm ring-1 ring-zinc-200">
                <p class="text-base font-bold text-zinc-700">Belum ada rumah sewa yang tersedia untuk dipaparkan.</p>
                <p class="mt-2 text-sm text-zinc-500">Sila semak semula selepas HEP mengesahkan maklumat rumah sewa.</p>
            </div>
        @endif
    </section>

    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="mb-6">
                <p class="text-sm font-extrabold uppercase tracking-wide text-orange-700">Cara guna portal</p>
                <h2 class="mt-1 text-2xl font-extrabold text-zinc-950">Langkah Ringkas</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach (['Pilih kawasan', 'Semak maklumat rumah', 'Hubungi pemilik', 'Laporkan jika maklumat tidak tepat'] as $index => $step)
                    <div class="rounded-2xl bg-orange-50 p-5 shadow-sm ring-1 ring-orange-100">
                        <span class="grid h-10 w-10 place-items-center rounded-2xl bg-blue-800 text-sm font-extrabold text-white">{{ $index + 1 }}</span>
                        <p class="mt-4 text-base font-extrabold text-zinc-950">{{ $step }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
