@php
    $user = filament()->auth()->user();
@endphp

@if ($user)
@php
    $user = filament()->auth()->user();
    $roleKey = $user?->getRoleNames()->first();
    $roleLabel = $roleKey ? (config("hep.roles.{$roleKey}") ?? $roleKey) : 'Pengguna Sistem';
@endphp

@if ($user)
    <div class="hep-topbar-tools">
        <a href="{{ route('home') }}" class="hep-topbar-portal">
            <x-lucide-house class="h-4 w-4" />
            <span>Ke Portal</span>
        </a>

        <div class="hep-user-summary">
            <div class="hep-user-summary-avatar">
                @if ($user->profilePhotoUrl())
                    <img src="{{ $user->profilePhotoUrl() }}" alt="Gambar profil {{ $user->name }}">
                @else
                    {{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
                @endif
            </div>

            <div class="hep-user-summary-text">
                <span class="hep-user-summary-label">{{ $roleLabel }}</span>
                <span class="hep-user-summary-name">{{ $user->name }}</span>
            </div>
        </div>
    </div>
@endif
@endif
