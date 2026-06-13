@php
    $thumbnailUrl = $property->thumbnailUrl();
    $whatsappUrl = $property->whatsappUrl();
    $mapsUrl = $property->mapsUrl();
    $statusValue = $property->status?->value ?? $property->status;
    $statusLabel = $property->status?->label() ?? $statusValue;
    $genderLabel = $property->gender_preference?->label() ?? $property->gender_preference;
    $statusClass = $statusValue === 'available'
        ? 'bg-emerald-100 text-emerald-800 ring-emerald-200'
        : 'bg-rose-100 text-rose-800 ring-rose-200';
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
    <div class="relative aspect-[4/3] overflow-hidden bg-emerald-50">
        @if ($thumbnailUrl)
            <img src="{{ $thumbnailUrl }}" alt="Gambar {{ $property->title }}" class="h-full w-full object-cover">
        @else
            <div class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-emerald-100 via-white to-amber-100 px-6 text-center">
                <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800">Rumah Sewa</span>
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
                <p class="shrink-0 text-right text-sm font-extrabold text-emerald-700">
                    RM{{ number_format((float) $property->price, 0) }}
                    <span class="block text-[11px] font-semibold text-zinc-500">sebulan</span>
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-zinc-600">
                <span class="rounded-full bg-zinc-100 px-2.5 py-1">{{ $property->area?->name ?? 'Kawasan tidak dinyatakan' }}</span>
                <span class="rounded-full bg-zinc-100 px-2.5 py-1">{{ $property->category?->name ?? 'Kategori tidak dinyatakan' }}</span>
                @if (filled($property->distance_km))
                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800">{{ number_format((float) $property->distance_km, 1) }} km</span>
                @endif
            </div>

            <p class="line-clamp-2 text-sm leading-6 text-zinc-600">
                {{ \Illuminate\Support\Str::limit(strip_tags($property->description), 120) }}
            </p>
        </div>

        @if ($keyFacilities->isNotEmpty())
            <div class="flex flex-wrap gap-2">
                @foreach ($keyFacilities as $facility)
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800 ring-1 ring-emerald-100">{{ $facility }}</span>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-3 gap-2">
            <a href="{{ route('properties.show', $property) }}" class="rounded-xl bg-zinc-950 px-3 py-2 text-center text-xs font-bold text-white shadow-sm transition hover:bg-zinc-800">
                Lihat Maklumat
            </a>
            @if ($whatsappUrl)
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="rounded-xl bg-emerald-600 px-3 py-2 text-center text-xs font-bold text-white shadow-sm transition hover:bg-emerald-700">
                    WhatsApp
                </a>
            @else
                <span class="rounded-xl bg-zinc-100 px-3 py-2 text-center text-xs font-bold text-zinc-400">WhatsApp</span>
            @endif
            @if ($mapsUrl)
                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener" class="rounded-xl bg-amber-400 px-3 py-2 text-center text-xs font-bold text-zinc-950 shadow-sm transition hover:bg-amber-300">
                    Peta
                </a>
            @else
                <span class="rounded-xl bg-zinc-100 px-3 py-2 text-center text-xs font-bold text-zinc-400">Peta</span>
            @endif
        </div>
    </div>
</article>
