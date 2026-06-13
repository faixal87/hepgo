<x-filament-widgets::widget>
    <x-filament::section>
        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Pengguna</div>
                <div class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $totalUsers }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Pengguna Aktif</div>
                <div class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $activeUsers }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Pemilik Rumah</div>
                <div class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $totalOwners }}</div>
            </div>

            <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Rumah Sewa</div>
                <div class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $totalProperties }}</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
