@php
    $user = filament()->auth()->user();
    $roleKey = $user?->getRoleNames()->first();
    $roleLabel = $roleKey ? (config("hep.roles.{$roleKey}") ?? $roleKey) : 'Pengguna Sistem';
@endphp

@if ($user)
    <div class="hep-topbar-tools">
        <a
            href="{{ route('home') }}"
            class="hep-topbar-portal"
            title="Portal Utama"
            aria-label="Portal Utama"
        >
            <x-lucide-house class="h-4 w-4" />
        </a>

        <div class="hep-user-summary">
            <div class="hep-user-summary-text">
                <span class="hep-user-summary-label">{{ $roleLabel }}</span>
                <span class="hep-user-summary-name">{{ $user->name }}</span>
            </div>
        </div>
    </div>
@endif
