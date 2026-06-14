@extends('layouts.public')

@section('title', $property->title.' | Portal Rumah Sewa HEP')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($property->description), 150))

@section('content')
    @php
        $mainImage = $property->mediumImageUrl();
        $whatsappUrl = $property->whatsappUrl();
        $mapsUrl = filled($property->maps_url) ? $property->maps_url : null;
        $directionUrl = $property->direction_url;
        $statusValue = $property->status?->value ?? $property->status;
        $statusClass = $statusValue === 'available'
            ? 'bg-orange-100 text-orange-800 ring-orange-200'
            : 'bg-blue-100 text-blue-800 ring-blue-200';
        $facilityNames = $property->facilities->pluck('name')
            ->merge(collect([
                'Parking' => $property->has_parking,
                'WiFi' => $property->has_wifi,
                'Mesin Basuh' => $property->has_washing_machine,
                'Dapur' => $property->has_kitchen,
                'Penyaman Udara' => $property->has_aircond,
            ])->filter()->keys())
            ->unique();
    @endphp

    <section class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <a href="{{ route('properties.index') }}" class="inline-flex rounded-full bg-white px-4 py-2 text-sm font-bold text-zinc-700 shadow-sm ring-1 ring-zinc-200 transition hover:bg-zinc-50">
            Kembali ke Senarai
        </a>
    </section>

    <section class="mx-auto grid max-w-7xl gap-8 px-4 pb-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
        <div class="space-y-4">
            <div
                class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-zinc-200"
                x-data="{ image: @js($mainImage) }"
            >
                @if ($mainImage)
                    <img :src="image" alt="Gambar {{ $property->title }}" loading="lazy" width="900" height="675" class="aspect-[4/3] w-full object-cover">
                @else
                    <div class="flex aspect-[4/3] w-full flex-col items-center justify-center gap-3 bg-gradient-to-br from-orange-100 via-white to-blue-100 px-6 text-center">
                        <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-800">Rumah Sewa</span>
                        <p class="text-base font-bold text-zinc-600">Gambar belum dimuat naik</p>
                    </div>
                @endif

                @if ($property->images->isNotEmpty())
                    <div class="grid grid-cols-4 gap-2 p-3 sm:grid-cols-6">
                        @foreach ($property->images as $image)
                            <button type="button" x-on:click="image = @js($image->largeUrl())" class="overflow-hidden rounded-xl ring-2 ring-transparent transition hover:ring-orange-500">
                                <img src="{{ $image->thumbnailUrl() }}" alt="{{ $image->caption ?: 'Gambar rumah sewa' }}" loading="lazy" width="160" height="160" class="aspect-square w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200">
                <h2 class="text-lg font-extrabold text-zinc-950">Maklumat Rumah</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-zinc-600">{{ $property->description }}</p>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200">
                <div class="flex flex-wrap gap-2">
                    <span class="rounded-full px-3 py-1 text-xs font-bold ring-1 {{ $statusClass }}">{{ $property->status?->label() }}</span>
                    <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-bold text-zinc-700 ring-1 ring-zinc-200">{{ $property->gender_preference?->label() }}</span>
                </div>

                <h1 class="mt-4 text-3xl font-extrabold leading-tight text-zinc-950">{{ $property->title }}</h1>
                <p class="mt-4 text-3xl font-extrabold text-orange-700">
                    RM{{ number_format((float) $property->price, 0) }}
                    <span class="text-sm font-bold text-zinc-500">sebulan</span>
                </p>

                <div class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="font-bold text-zinc-500">Kawasan</p>
                        <p class="mt-1 font-extrabold text-zinc-950">{{ $property->area?->name ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="font-bold text-zinc-500">Kategori</p>
                        <p class="mt-1 font-extrabold text-zinc-950">{{ $property->category?->name ?? 'Tidak dinyatakan' }}</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="font-bold text-zinc-500">Jarak Dari POLIMAS</p>
                        <p class="mt-1 font-extrabold text-zinc-950">
                            {{ filled($property->distance_km) ? number_format((float) $property->distance_km, 1).' km' : 'Tidak dinyatakan' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="font-bold text-zinc-500">Deposit</p>
                        <p class="mt-1 font-extrabold text-zinc-950">
                            {{ filled($property->deposit) ? 'RM'.number_format((float) $property->deposit, 0) : 'Tidak dinyatakan' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-orange-50 p-4">
                        <p class="font-bold text-orange-700">Tarikh Disiarkan</p>
                        <p class="mt-1 font-extrabold text-zinc-950">{{ $property->created_at?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div class="rounded-2xl bg-blue-50 p-4">
                        <p class="font-bold text-blue-700">Terakhir Dikemaskini</p>
                        <p class="mt-1 font-extrabold text-zinc-950">{{ $property->updated_at?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-5 rounded-2xl bg-orange-50 p-4 ring-1 ring-orange-100">
                    <p class="text-sm font-bold text-orange-900">Alamat</p>
                    <p class="mt-2 text-sm leading-6 text-orange-950">{{ $property->address }}</p>
                </div>
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200">
                <h2 class="text-lg font-extrabold text-zinc-950">Kemudahan Rumah</h2>
                <div class="mt-4 grid grid-cols-3 gap-3 text-center text-sm">
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="text-2xl font-extrabold text-zinc-950">{{ $property->total_rooms ?? '-' }}</p>
                        <p class="mt-1 font-bold text-zinc-500">Bilik</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="text-2xl font-extrabold text-zinc-950">{{ $property->total_bathrooms ?? '-' }}</p>
                        <p class="mt-1 font-bold text-zinc-500">Bilik Air</p>
                    </div>
                    <div class="rounded-2xl bg-zinc-50 p-4">
                        <p class="text-2xl font-extrabold text-zinc-950">{{ $property->max_occupants ?? '-' }}</p>
                        <p class="mt-1 font-bold text-zinc-500">Penghuni</p>
                    </div>
                </div>

                @if ($facilityNames->isNotEmpty())
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($facilityNames as $facility)
                            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-800 ring-1 ring-blue-100">{{ $facility }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200">
                <h2 class="text-lg font-extrabold text-zinc-950">Maklumat Pemilik</h2>
                <div class="mt-4 space-y-2 text-sm text-zinc-600">
                    <p><span class="font-bold text-zinc-950">Nama:</span> {{ $property->owner?->name ?? 'Tidak dinyatakan' }}</p>
                    <p><span class="font-bold text-zinc-950">No. Telefon:</span> {{ $property->owner?->phone ?? 'Tidak dinyatakan' }}</p>
                    <p><span class="font-bold text-zinc-950">No. WhatsApp:</span> {{ $property->owner?->whatsapp_number ?? 'Tidak dinyatakan' }}</p>
                </div>

                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-center text-sm font-extrabold text-white shadow-sm transition hover:bg-emerald-700">
                            <x-lucide-message-circle class="h-5 w-5 shrink-0" />
                            <span>WhatsApp</span>
                        </a>
                    @endif

                    @if ($mapsUrl)
                        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-orange-500 px-4 py-3 text-center text-sm font-extrabold text-white shadow-sm transition hover:bg-orange-600">
                            <x-lucide-map-pinned class="h-5 w-5 shrink-0" />
                            <span>Peta Rumah</span>
                        </a>
                    @endif

                    @if ($directionUrl)
                        <a href="{{ $directionUrl }}" target="_blank" rel="noopener" class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-blue-600 px-4 py-3 text-center text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700">
                            <x-lucide-route class="h-5 w-5 shrink-0" />
                            <span>Arah Ke POLIMAS</span>
                        </a>
                    @endif

                    <a href="{{ route('reports.create.property', $property) }}" class="inline-flex min-h-14 items-center justify-center gap-2 rounded-2xl bg-zinc-100 px-4 py-3 text-center text-sm font-extrabold text-zinc-700 ring-1 ring-zinc-200 transition hover:bg-zinc-200">
                        <x-lucide-flag class="h-5 w-5 shrink-0" />
                        <span>Laporkan Maklumat</span>
                    </a>
                </div>
                @if (filled($property->distance_km))
                    <p class="mt-3 text-xs font-semibold leading-5 text-zinc-500">Jarak anggaran: {{ number_format((float) $property->distance_km, 1) }} km dari POLIMAS</p>
                @endif
            </div>

            <div class="rounded-3xl bg-orange-50 p-5 text-sm font-semibold leading-7 text-orange-950 ring-1 ring-orange-100">
                Nota: HEP menyediakan maklumat ini sebagai rujukan. Sila semak sendiri keadaan rumah dan persetujuan sewaan sebelum membuat sebarang bayaran.
            </div>
        </div>
    </section>
@endsection
