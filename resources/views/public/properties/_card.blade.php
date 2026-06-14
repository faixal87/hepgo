@php
    $thumbnailUrl = $property->thumbnailUrl();
    $imageUrls = $property->images
        ->sortBy([
            ['is_thumbnail', 'desc'],
            ['sort_order', 'asc'],
            ['id', 'asc'],
        ])
        ->map(fn ($image) => $image->thumbnailUrl())
        ->filter()
        ->values();

    if ($imageUrls->isEmpty() && $thumbnailUrl) {
        $imageUrls = collect([$thumbnailUrl]);
    }

    $whatsappUrl = $property->whatsappUrl();
    $mapsUrl = filled($property->maps_url) ? $property->maps_url : null;
    $directionUrl = $property->direction_url;
    $statusValue = $property->status?->value ?? $property->status;
    $statusLabel = $property->status?->label() ?? $statusValue;
    $genderLabel = $property->gender_preference?->label() ?? $property->gender_preference;
    $statusClass = $statusValue === 'available'
        ? 'bg-orange-100 text-orange-800 ring-orange-200'
        : 'bg-blue-100 text-blue-800 ring-blue-200';
    $facilityNames = $property->facilities->pluck('name');
    $booleanFacilities = collect([
        'Parking' => $property->has_parking,
        'WiFi' => $property->has_wifi,
        'Mesin Basuh' => $property->has_washing_machine,
        'Dapur' => $property->has_kitchen,
        'Penyaman Udara' => $property->has_aircond,
    ])->filter()->keys();
    $keyFacilities = $facilityNames->merge($booleanFacilities)->unique()->take(4);
@endphp

<article class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-zinc-200/80 transition hover:-translate-y-0.5 hover:shadow-md">
    <div
        class="relative aspect-[4/3] overflow-hidden bg-orange-50"
        @if ($imageUrls->count() > 1)
            x-data="{ active: 0, images: @js($imageUrls), timer: null }"
            x-init="timer = setInterval(() => active = (active + 1) % images.length, 3500)"
            x-on:mouseenter="clearInterval(timer)"
            x-on:mouseleave="timer = setInterval(() => active = (active + 1) % images.length, 3500)"
        @endif
    >
        @if ($imageUrls->isNotEmpty())
            @if ($imageUrls->count() > 1)
                <template x-for="(image, index) in images" :key="image">
                    <img
                        :src="image"
                        alt="Gambar {{ $property->title }}"
                        loading="lazy"
                        width="400"
                        height="300"
                        class="absolute inset-0 h-full w-full object-cover transition-opacity duration-700"
                        x-bind:class="active === index ? 'opacity-100' : 'opacity-0'"
                    >
                </template>

                <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1.5">
                    @foreach ($imageUrls as $image)
                        <span class="h-1.5 w-1.5 rounded-full bg-white/90 shadow ring-1 ring-zinc-900/10"></span>
                    @endforeach
                </div>
            @else
                <img src="{{ $imageUrls->first() }}" alt="Gambar {{ $property->title }}" loading="lazy" width="400" height="300" class="h-full w-full object-cover">
            @endif
        @else
            <div class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-orange-100 via-white to-blue-100 px-6 text-center">
                <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-800">Rumah Sewa</span>
                <span class="text-sm font-semibold text-zinc-600">Gambar belum dimuat naik</span>
            </div>
        @endif

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            <span class="rounded-full px-3 py-1 text-xs font-bold shadow-sm ring-1 {{ $statusClass }}">{{ $statusLabel }}</span>
            <span class="rounded-full bg-white/90 px-3 py-1 text-xs font-bold text-zinc-700 shadow-sm ring-1 ring-zinc-200">{{ $genderLabel }}</span>
        </div>
    </div>

    <div class="space-y-4 p-4">
        <div class="space-y-2">
            <div class="flex items-start justify-between gap-3">
                <h3 class="line-clamp-2 text-base font-extrabold leading-snug text-zinc-950">
                    {{ $property->title }}
                </h3>
                <p class="shrink-0 text-right text-sm font-extrabold text-orange-700">
                    RM{{ number_format((float) $property->price, 0) }}
                    <span class="block text-[11px] font-semibold text-zinc-500">sebulan</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-zinc-600">
                <span class="rounded-full bg-zinc-100 px-2.5 py-1">{{ $property->area?->name ?? 'Kawasan tidak dinyatakan' }}</span>
                <span class="rounded-full bg-zinc-100 px-2.5 py-1">{{ $property->category?->name ?? 'Kategori tidak dinyatakan' }}</span>
                @if (filled($property->distance_km))
                    <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800">{{ number_format((float) $property->distance_km, 1) }} km dari POLIMAS</span>
                @endif
            </div>

            <p class="line-clamp-2 text-sm leading-6 text-zinc-600">
                {{ \Illuminate\Support\Str::limit(strip_tags($property->description), 120) }}
            </p>

            <div class="flex flex-wrap items-center gap-2 text-[11px] font-bold text-zinc-500">
                <span>Disiarkan: {{ $property->created_at?->format('d/m/Y') ?? '-' }}</span>
                @if ($property->updated_at && $property->created_at && $property->updated_at->gt($property->created_at->copy()->addMinute()))
                    <span class="text-blue-700">Dikemaskini: {{ $property->updated_at->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>

        @if ($keyFacilities->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($keyFacilities as $facility)
                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-800 ring-1 ring-blue-100">{{ $facility }}</span>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-4 gap-2">
            <a href="{{ route('properties.show', $property) }}" title="View Info" aria-label="View info {{ $property->title }}" class="flex h-16 min-w-0 flex-col items-center justify-center gap-1 rounded-xl bg-blue-900 px-1.5 py-2 text-center text-[11px] font-extrabold leading-tight text-white shadow-sm transition hover:bg-blue-800">
                <x-lucide-info class="h-5 w-5 shrink-0" />
                <span class="block max-w-full">Info</span>
            </a>
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" title="WhatsApp" aria-label="Hubungi pemilik melalui WhatsApp" class="flex h-16 min-w-0 flex-col items-center justify-center gap-1 rounded-xl bg-emerald-600 px-1.5 py-2 text-center text-[11px] font-extrabold leading-tight text-white shadow-sm transition hover:bg-emerald-700">
                    <x-lucide-message-circle class="h-5 w-5 shrink-0" />
                    <span class="block max-w-full text-[9.5px]">WhatsApp</span>
                </a>
            @else
                <span title="WhatsApp tidak tersedia" aria-label="WhatsApp tidak tersedia" class="flex h-16 min-w-0 flex-col items-center justify-center gap-1 rounded-xl bg-zinc-100 px-1.5 py-2 text-center text-[11px] font-extrabold leading-tight text-zinc-400">
                    <x-lucide-message-circle class="h-5 w-5 shrink-0" />
                    <span class="block max-w-full text-[9.5px]">WhatsApp</span>
                </span>
            @endif
            @if ($mapsUrl)
                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" title="House Map" aria-label="Open house location in Google Maps" class="flex h-16 min-w-0 flex-col items-center justify-center gap-1 rounded-xl bg-orange-500 px-1.5 py-2 text-center text-[11px] font-extrabold leading-tight text-white shadow-sm transition hover:bg-orange-600">
                    <x-lucide-map-pinned class="h-5 w-5 shrink-0" />
                    <span class="block max-w-full">Map</span>
                </a>
            @endif
            @if ($directionUrl)
                <a href="{{ $directionUrl }}" target="_blank" rel="noopener" title="Directions to POLIMAS" aria-label="Open directions from house to POLIMAS" class="flex h-16 min-w-0 flex-col items-center justify-center gap-1 rounded-xl bg-blue-600 px-1.5 py-2 text-center text-[11px] font-extrabold leading-tight text-white shadow-sm transition hover:bg-blue-700">
                    <x-lucide-route class="h-5 w-5 shrink-0" />
                    <span class="block max-w-full">Route</span>
                </a>
            @endif
        </div>
    </div>
</article>
