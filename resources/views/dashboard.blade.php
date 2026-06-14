<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-extrabold leading-tight text-slate-900">
            Papan Pemuka Pengguna
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <p class="text-sm font-bold uppercase tracking-wide" style="color: var(--hep-accent);">Selamat datang</p>
                <h3 class="mt-2 text-2xl font-black text-slate-950">{{ auth()->user()->name }}</h3>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600">
                    Anda telah log masuk ke workspace pengguna Portal Rumah Sewa HEP. Gunakan menu sisi untuk mengemaskini profil, gambar profil dan tema paparan.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
