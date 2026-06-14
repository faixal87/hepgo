@extends('layouts.public')

@section('title', 'Senarai Rumah Sewa')
@section('meta_description', 'Semak senarai rumah sewa luar kampus yang telah disahkan oleh HEP.')

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div class="max-w-3xl space-y-3">
                <p class="text-sm font-extrabold uppercase tracking-wide text-orange-700">Carian rumah sewa</p>
                <h1 class="text-3xl font-extrabold text-zinc-950 sm:text-4xl">Senarai Rumah Sewa</h1>
                <p class="text-base leading-7 text-zinc-600">
                    Gunakan carian dan tapisan untuk mencari rumah sewa yang sesuai mengikut kawasan, bajet, kategori, kemudahan dan keutamaan penyewa.
                </p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <livewire:property-search />
    </section>
@endsection
