@extends('layouts.public')

@section('title', 'Hantar Aduan | Portal Rumah Sewa HEP')
@section('meta_description', 'Hantar aduan berkaitan maklumat rumah sewa luar kampus kepada HEP.')

@section('content')
    <section class="bg-white">
        <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
            <p class="text-sm font-extrabold uppercase tracking-wide text-emerald-700">Aduan Maklumat Rumah Sewa</p>
            <h1 class="mt-2 text-3xl font-extrabold text-zinc-950 sm:text-4xl">Hantar Aduan</h1>
            <p class="mt-3 text-base leading-7 text-zinc-600">
                Maklumkan kepada HEP jika maklumat rumah sewa tidak tepat, pemilik tidak dapat dihubungi, atau rumah sudah penuh.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-6 rounded-2xl bg-emerald-50 p-4 text-sm font-bold text-emerald-900 ring-1 ring-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 p-4 text-sm font-semibold text-rose-900 ring-1 ring-rose-100">
                <p class="font-extrabold">Sila semak semula maklumat aduan.</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('reports.store') }}" class="space-y-6 rounded-3xl bg-white p-5 shadow-sm ring-1 ring-zinc-200 sm:p-6">
            @csrf

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="property_id" class="text-sm font-bold text-zinc-700">Rumah Sewa Berkaitan</label>
                    <select id="property_id" name="property_id" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Tidak pasti / aduan umum</option>
                        @foreach ($properties as $reportProperty)
                            <option value="{{ $reportProperty->id }}" @selected((string) old('property_id', $property?->id) === (string) $reportProperty->id)>
                                {{ $reportProperty->title }}{{ $reportProperty->area ? ' - '.$reportProperty->area->name : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="reporter_name" class="text-sm font-bold text-zinc-700">Nama Pengadu</label>
                    <input id="reporter_name" name="reporter_name" type="text" value="{{ old('reporter_name') }}" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="reporter_phone" class="text-sm font-bold text-zinc-700">No. Telefon Pengadu</label>
                    <input id="reporter_phone" name="reporter_phone" type="tel" value="{{ old('reporter_phone') }}" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="reporter_email" class="text-sm font-bold text-zinc-700">Emel Pengadu</label>
                    <input id="reporter_email" name="reporter_email" type="email" value="{{ old('reporter_email') }}" class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="report_type" class="text-sm font-bold text-zinc-700">Jenis Aduan</label>
                    <select id="report_type" name="report_type" required class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih jenis aduan</option>
                        @foreach ($reportTypes as $value => $label)
                            <option value="{{ $value }}" @selected(old('report_type') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label for="message" class="text-sm font-bold text-zinc-700">Mesej Aduan</label>
                    <textarea id="message" name="message" rows="6" required class="mt-2 w-full rounded-2xl border-zinc-200 text-sm font-semibold shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Terangkan maklumat yang tidak tepat atau perkara yang perlu disemak oleh HEP.">{{ old('message') }}</textarea>
                </div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs font-semibold leading-5 text-zinc-500">
                    Maklumat aduan akan digunakan oleh HEP untuk semakan dalaman sahaja.
                </p>
                <button type="submit" class="rounded-2xl bg-emerald-700 px-6 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-emerald-800">
                    Hantar Aduan
                </button>
            </div>
        </form>
    </section>
@endsection
